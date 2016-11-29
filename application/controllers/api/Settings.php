<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * RESTful Settings
 * 
 * Retrieve, delete, create and update settings through the RESTful api * 
 * http://yourdomain.com/index.php/api/settings
 */
class Settings extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        
        if (! isset($this->settings_model))
            $this->load->model('settings_model');
    }

    /**
     * update
     * 
     * url:  http://yourdomain.com/index.php/api/settings/update
     * type: POST
     *
     * Update settings
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int       $user_id            required    user_id of the person who is making the request
     * @param string    $setting_name       required    name of the setting you are updating. Possible names: currency_symbol, invoice_number, estimate_number, address
     * @param string    $setting_value      required    setting value
     * 
     * @return json object
     */
    public function update_post()
    {
        $user_id = $this->post('user_id', true);
        $setting_name = $this->post('setting_name', true);
        $setting_value = $this->post('setting_value', true);
        
        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
        
        if (! $user_id || $user_id == 0) {
            $response['message'] = $this->lang->line("user_id_required");
            
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
        	$originalSettingName = $setting_name;
        	$originalSettingValue = $setting_value;
        	
        	if ($setting_name == "logo_base64")
        	{
        		if (! isset($this->users_model)) $this->load->model('users_model');        		
        		$user = $this->users_model->get(array('id'=>$user_id));
        		
        		if (isset($user[0]))
        		{
	        		$logoBase64 = $setting_value;
	        		
	        		$image = base64_decode($setting_value);
	        		$user_upload_path = APPPATH . '../assets/uploads/'.$user[0]->rid.'/';
	        		
	        		if (!is_dir($user_upload_path))
	        		{
	        			mkdir($user_upload_path);
	        		}
	        		
	        		file_put_contents($user_upload_path.'logo.png', $image);
	        		
	        		$setting_value = 'logo.png';
	        		$setting_name = 'logo_path';
        		}
        		else 
        		{
        			$setting_value = '';
        			$setting_name = 'logo_path';
        		}
        	}
        	
            $data['user_id'] = $user_id;
            $data['setting_name'] = $setting_name;
            $data['setting_value'] = $setting_value;
            
            if ($result = $this->settings_model->update($data)) {
                $response['message'] = $this->lang->line("record_creation_successful");
                $response['success'] = true;
                
                if ($originalSettingName == 'logo_base64')
                {
                	$setting_name = $originalSettingName;
                	$setting_value = $setting_value != '' ? $originalSettingValue : '';
                }
                
                $response['data']["setting_name"] = $setting_name;
                $response['data']["setting_value"] = $setting_value;
                
                $this->set_response($response, REST_Controller::HTTP_CREATED);
            } else {
                $response['message'] = $this->lang->line("record_creation_failed");
                
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * get
     * 
     * url:  http://yourdomain.com/index.php/api/settings/get
     * type: POST
     *
     * Retrieve users setting information
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
        
        $message = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );
        
        if (! $user_id || $user_id == 0 || ! $api_key) {
            $response['message'] = $this->lang->line("text_rest_invalid_credentials");
            
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->settings_model->db->join('users', 'users.id = settings.user_id', 'left');
            $this->settings_model->db->join('keys', 'keys.id = users.api_key', 'left');
            
            $settings = $this->settings_model->get(array(
                'settings.user_id' => $user_id,
                'keys.key' => $api_key
            ), "settings.*, users.rid as user_rid");
            
            $data = array();
            
            if (isset($settings[0]))
            {
            	$content = json_decode($settings[0]->content);
            	
            	$user_upload_path = APPPATH . '../assets/uploads/'.$settings[0]->user_rid.'/';
            	
            	$data["logo"] =  empty($content->logo_path) ? "" : base64_encode(file_get_contents(base_url("assets/uploads/".$settings[0]->user_rid.'/'.$content->logo_path)));
            	$data['currency_symbol'] = $content->currency_symbol;
            	$data['invoice_number'] = $content->invoice_number;
            	$data['estimate_number'] = $content->estimate_number;
            }
            
            $response['message'] = "";
            $response['success'] = true;
            $response['data'] = new stdClass();
            $response['data']->settings = $data;
            
            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }
}