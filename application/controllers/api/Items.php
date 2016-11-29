<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RESTful Items
 * 
 * Retrieve, delete, create and update items through the RESTful api * 
 * http://yourdomain.com/index.php/api/items
 */
class Items extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        
        if (! isset($this->items_model))
            $this->load->model('items_model');
    }

    /**
     * create
     * 
     * url:  http://yourdomain.com/index.php/api/items/create
     * type: POST
     *
     * Create a new item using RESTful API
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int       $user_id        required     user_id of the person who is making the request
     * @param int       $name           required     Item name
     * @param int       $id             optional     item id
     * @param float     $rate           optional     Item rate
     * @param string    $description    optional     item description
     * 
     * @return jsob object
     */
    public function create_post()
    {
        $id = $this->post('id', true);
        $user_id = $this->post('userId', true);
        $name = $this->post('itemName', true);
        $description = $this->post('description', true);
        $rate = $this->post('rate', true);
        $cost = $this->post('cost', true);
        $tags = $this->post('tags', true);
        $taxable = $this->post('taxable', true);
        
        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
        
        if (! $name || ! $user_id || $user_id == 0) {
            $response['message'] = lang("name_user_id_required");
            
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($id){
                $data['id'] = $id;
            }
            
            $data['user_id'] = $user_id;
            $data['name'] = $name;
            $data['description'] = $description;
            $data['rate'] = $rate;
            $data['cost'] = $cost;
            $data['tags'] = $tags;
            $data['taxable'] = $taxable;
            
            if ($result = $this->items_model->save($data)) {
                $response['message'] = lang("record_creation_successful");
                $response['success'] = true;
                
                $this->set_response($response, REST_Controller::HTTP_CREATED);
            } else {
                $response['message'] = lang("record_creation_failed");
                
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * get
     * 
     * url:  http://yourdomain.com/index.php/api/items/get
     * type: POST
     *
     * Retrieve items for a specific user
     *
     * @global string HTTP_X_API_KEY                required    api key of the current logged in user
     *
     * @param int    $user_id                       required    user_id of the person who is making the request
     * 
     * @return json object
     */
    public function get_post()
    {
        $user_id = $this->post('userId', true);
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        
        $message = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
        
        if (! $user_id || $user_id == 0) {
            $response['message'] = lang("text_rest_invalid_credentials");
            
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            
//            $this->items_model->db->join('users', 'users.id = items.user_id', 'left');
//            $this->items_model->db->join('keys', 'keys.id = users.api_key', 'left');
            $items = $this->items_model->get(array(
                'items.user_id' => $user_id,
            ), "items.id as itemId, items.name as itemName, items.description, items.rate", 'created_on desc');
           
            $response['message'] = "";
            $response['success'] = true;
            
            $response['data']['items'] = $items;
            
            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }

    /**
     * delete
     * 
     * url:  http://yourdomain.com/index.php/api/items/delete
     * type: POST
     *
     * Delete item
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int    $user_id           required    user_id of the person who is making the request
     * @param int    $id                required    item id
     * 
     * @return json object
     */
    public function delete_post()
    {
        $user_id = $this->post('userId');
        $id = $this->post('itemId', true);
        $itemIds = implode(',', $id);
        
        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
        
        if (! $user_id || $user_id == 0) {
            $response['message'] = lang("text_rest_invalid_credentials");
            
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else 
            if (! $id) {
                $response['message'] = lang("item_required");
                
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if ($this->items_model->delete($user_id, $itemIds)) {
                    $response['success'] = true;
                    $this->set_response($response, REST_Controller::HTTP_OK);
                } else {
                    $response['message'] = lang("request_failed");
                    $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
                }
            }
    }
}