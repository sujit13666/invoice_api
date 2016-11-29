<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RESTful Estimates
 * 
 * Retrieve, delete, create and update estimates through the RESTful api * 
 * http://yourdomain.com/index.php/api/estimates
 */
class Estimates extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        
        if (! isset($this->estimates_model))
            $this->load->model('estimates_model');
        if (! isset($this->estimates_lines_model))
            $this->load->model('estimates_lines_model');
    }

    /**
     * create
     * 
     * url: http://yourdomain.com/index.php/api/estimates/create
     * type: POST
     *
     * Create a new estimate using RESTful API
     * 
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     * 
     * @param int   $user_id           required     user_id of the person who is making the request
     * @param int   $estimate_number   required     estimate number #
     * @param int   $id                optional     estimate id  
     * @param int   $estimate_date     optional     unixtime
     * @param int   $due_date          optional     unixtime
     * @param int   $client_id         optional
     * @param float  $tax_rate         optional     percentage value from 0 - 100 
     * @param string $notes            optional     Any free form text, estimate note
     * @param array $items             required     list of items ('Name'=>'item1','Description'=>'item description','Rate'=>25.00,'Quantity'=>5)
     * 
     * @return json object
     */
    public function create_post()
    {
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        $id = $this->post('id', true);
        $user_id = $this->post('user_id', true);
        $estimate_number = $this->post('estimate_number', true);
        $tax_rate = $this->post('tax_rate', true);
        
        $estimate_date = $this->post('estimate_date', true);
        $due_date = $this->post('due_date', true);
        $client_id = $this->post('client_id', true);
        $notes = $this->post('notes', true);
        
        $items = json_decode($this->post('items', true));
        
        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
        
        if (! $estimate_number || ! $user_id || $user_id == 0) {
            $response['message'] = lang("estimate_number_user_id_required");
            
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($id)
                $estimate['id'] = $id;
            
            $estimate['user_id'] = $user_id;
            $estimate['estimate_number'] = $estimate_number;
            $estimate['tax_rate'] = $tax_rate;
            
            $estimate['estimate_date'] = $estimate_date;
            $estimate['due_date'] = $due_date;
            $estimate['client_id'] = $client_id;
            $estimate['notes'] = $notes;
            
            if ($estimate = $this->estimates_model->save($estimate)) {
                if ($id) {
                    $this->estimates_lines_model->delete_where('estimate_id = ' . $estimate->id);
                } else {
                    // If new record update invoice number in settings
                    if (! isset($this->settings_model))
                        $this->load->model('settings_model');
                    
                    $this->settings_model->db->join('users', 'users.id = settings.user_id', 'left');
                    $this->settings_model->db->join('keys', 'keys.id = users.api_key', 'left');
                    $settings = $this->settings_model->get(array(
                        'settings.user_id' => $user_id,
                        'keys.key' => $api_key
                    ), "settings.*", null, 1);
                    
                    if ($settings) {
                        $array_settings = (array) $settings[0];
                        
                        $content = (array) json_decode($array_settings['content']);
                        
                        $content['estimate_number'] += 1;
                        
                        $array_settings['content'] = json_encode($content);
                        
                        $this->settings_model->save($array_settings);
                    }
                }
                
                foreach ($items as $key => $item) {
                    $estimate_item = [];
                    
                    $estimate_item['user_id'] = $user_id;
                    $estimate_item['estimate_id'] = $estimate->id;
                    $estimate_item['name'] = $item->Name;
                    $estimate_item['description'] = $item->Description;
                    $estimate_item['rate'] = $item->Rate;
                    $estimate_item['quantity'] = $item->Quantity;
                    
                    $this->estimates_lines_model->save($estimate_item);
                }
                
                $response['message'] = lang("record_creation_successful");
                $response['success'] = true;
                
                $this->set_response($response, REST_Controller::HTTP_CREATED);
            } else {
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * get
     * 
     * url:  http://yourdomain.com/index.php/api/estimates/get
     * type: POST
     *
     * Retrieve estimates for a specific user
     *
     * @global string HTTP_X_API_KEY                required    api key of the current logged in user
     *
     * @param int    $user_id                       required    user_id of the person who is making the request
     * 
     * @return json object
     */
    public function get_post()
    {
        $user_id = $this->post('user_id', true);
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        
        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
        
        if (! $user_id || $user_id == 0 || ! $api_key) {
            $response['message'] = lang("text_rest_invalid_credentials");
            
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->estimates_model->db->join('estimates_lines', 'estimates_lines.estimate_id = estimates.id', 'left');
            $this->estimates_model->db->join('clients', 'clients.id = estimates.client_id', 'left');
            $this->estimates_model->db->join('users', 'users.id = estimates.user_id', 'left');
            $this->estimates_model->db->join('keys', 'keys.id = users.api_key', 'left');
            $this->estimates_model->db->group_by('estimates.id');
            
            $select = 'estimates.*, clients.name as client_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total';
            
            $estimates = $this->estimates_model->get(array(
                'estimates.user_id' => $user_id,
                'keys.key' => $api_key
            ), $select, 'estimate_date asc, created_on desc');
            
            $response['message'] = "";
            $response['success'] = true;
            
            $response['data']['estimates'] = $estimates;
            
            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }

    /**
     * delete
     * 
     * url:  http://yourdomain.com/index.php/api/estimates/delete
     * type: POST
     *
     * Delete estimate
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int    $user_id           required    user_id of the person who is making the request
     * @param int    $id                required    estimate id
     * 
     * @return json object
     */
    public function delete_post()
    {
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        $user_id = $this->post('user_id');
        $id = $this->post('id');
        
        $message = array(
            'success' => false,
            'message' => ''
        );
        
        if (! $user_id || $user_id == 0 || ! $api_key) {
            $message['message'] = lang("text_rest_invalid_credentials");
            
            $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        } else 
            if (! $id) {
                $message['message'] = lang("estimate_required");
                
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if ($this->estimates_model->delete($api_key, $user_id, $id)) {
                    $this->set_response($message, REST_Controller::HTTP_OK);
                } else {
                    $message['message'] = lang("request_failed");
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            }
    }
    
    /**
     * invoiced
     * 
     * url:  http://yourdomain.com/index.php/api/estimates/invoiced
     * type: POST
     *
     * Mark estimate as invoiced
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int    $user_id           required    user_id of the person who is making the request
     * @param int    $id                required    estimate id
     * 
     * @return json object
     */
    public function invoiced_post()
    {
        $id = $this->post('id', true);
        $user_id = $this->post('user_id', true);
    
        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
    
        if (! $id || ! $user_id || $user_id == 0) {
            $response['message'] = lang("text_invalid_request");
    
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $estimate = array();
            $estimate['id'] = $id;
            $estimate['user_id'] = $user_id;
            $estimate['is_invoiced'] = 1;
    
            if ($estimate = $this->estimates_model->save($estimate)) {
                $response['message'] = lang("record_creation_successful");
                $response['success'] = true;
    
                $this->set_response($response, REST_Controller::HTTP_CREATED);
            } else {
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }
}