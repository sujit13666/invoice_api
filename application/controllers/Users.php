<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Users controller
 *
 * All user action using the website is done here
 * This is mostly only visible to an admin
 */
class Users extends MY_Controller
{

    public function __construct()
    {
        $this->need_auth = TRUE;
        
        parent::__construct();
        
        // Make sure normal user can't access any other method than setting
        if ($this->method != "settings") {
            // Only admins can see this sections
            if (! $this->ion_auth->is_admin())
                redirect(base_url().'auth', 'refresh');
        }
        
        $this->load->library(array(
            'form_validation'
        ));
    }

    /**
     *
     * index
     *
     * This will show the user list page
     *
     * @param int $page optional is used for pagination. To go to the second, third etc. page
     */
    public function index($page = 0)
    {
        // Default values
        $items = array();
        $total_rows = 0;
        $uri_segment = 3;
        $search = '';
        
        // Dealing with search
        if ($this->input->post('search_button')) {
            $search = $this->data['search'] = $this->input->post('search_text', true);
            $this->session->set_userdata($this->controller . 'search', $search);
        } else 
            if ($this->session->userdata($this->controller . 'search')) {
                $search = $this->data['search'] = ($this->session->userdata($this->controller . 'search'));
            }
        
        // Search
        if ($search) {
            $this->object_model->db->like(array(
                'users.first_name' => $search
            ));
            $this->object_model->db->or_like(array(
                'users.last_name' => $search
            ));
            $this->object_model->db->or_like(array(
                'users.email' => $search
            ));
        }
        
        $this->object_model->db->join('users_groups_rel', 'users_groups_rel.user_id = users.id');
        $this->object_model->db->join('groups', 'groups.id = users_groups_rel.group_id');
        $this->object_model->db->join('settings', 'settings.user_id = users.id', 'left');
        $users = $this->object_model->get(false, "users.*, settings.content, groups.name as group", 'created_on desc', $this->rows_per_page, $page);
        
        if ($users) {
            for ($i = 0; $i < count($users); $i ++) {
                $user = $users[$i];
                if (isset($user->content)) {
                    $content = json_decode($user->content);
                    $users[$i]->currency_symbol = $content->currency_symbol;
                    
                    $address = explode("|#", $content->address);
                    $users[$i]->address = '';
                    
                    if ($address) {
                        foreach ($address as $key => $val) {
                            if (trim($val) != '')
                                $users[$i]->address .= $val . ', ';
                        }
                        $users[$i]->address = trim($users[$i]->address, ', ');
                    }
                } else {
                    $users[$i]->currency_symbol = '';
                    $users[$i]->address = '';
                }
            }
        }
        
        // Search
        if ($search) {
            $this->object_model->db->like(array(
                'users.first_name' => $search
            ));
            $this->object_model->db->or_like(array(
                'users.last_name' => $search
            ));
            $this->object_model->db->or_like(array(
                'users.email' => $search
            ));
        }
        
        $total_rows = $this->object_model->count();
        
        $this->data['result'] = $users;
        
        $this->data['total_rows'] = $total_rows;
        $this->data['rows_per_page'] = $this->rows_per_page;
        
        // When searching reset
        $this->data['offset'] = $search ? 0 : $page;
        
        $config['base_url'] = site_url($this->controller . '/' . $this->method . '/' . $this->config->item('pagination_selector'));
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->rows_per_page;
        $config['uri_segment'] = $uri_segment;
        $config['next_link'] = '&#8250;';
        $config['prev_link'] = '&#8249;';
        
        // initialising Codeigniters pagination class
        $this->pagination->initialize($config);
        
        $this->data['pagination'] = $this->pagination->create_links();
        
        $this->template();
    }

    /**
     *
     * edit
     *
     * This method deals with creating and editing a new user.
     *
     * @param string $rid optional This property defines whether it's a new user or existing user. It will be empty for a new user.
     * 
     */
    function edit($rid = FALSE)
    {
        $user = FALSE;
        
        if ($rid) {
            $this->object_model->db->join('users_groups_rel', 'users_groups_rel.user_id = users.id');
            $this->object_model->db->join('settings', 'settings.user_id = users.id', 'left');
            $user = $this->object_model->get(array(
                'users.rid' => $rid
            ), 'users.*, settings.content, settings.rid as settings_rid, users_groups_rel.rid as users_groups_rid, users_groups_rel.group_id');
            
            if ($user && isset($user[0])) {
                $content = json_decode($user[0]->content);
                $user[0]->currency_symbol = $content->currency_symbol;
                
                $address = explode('|#', $content->address);
                
                $user[0]->address1 = $address[0];
                $user[0]->address2 = $address[1];
                $user[0]->city = $address[2];
                $user[0]->state = $address[3];
                $user[0]->postcode = $address[4];
                $user[0]->country = $address[5];
                
                $this->data['result'] = $user[0];
            } else {
                // If no record found redirect to index page
                redirect(base_url().$this->controller, 'refresh');
            }
        } else {
            $this->data['title'] = lang($this->controller . '_' . 'new');
        }
        
        if (isset($_POST['fn']) and $_POST['fn'] == md5($this->controller . $this->method)) {
            $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
            $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
            $this->form_validation->set_rules('email', lang('email'), 'required');
            $this->form_validation->set_rules('group_id', lang('group_id'), 'required');
            
            if (! $rid)
                $this->form_validation->set_rules('password', lang('password'), 'required');
            
            if ($this->form_validation->run() == true) {
                $item = array();
                
                if ($rid) {
                    if ($user)
                        $item['id'] = $user[0]->id;
                } else {
                    if (! isset($this->keys_model))
                        $this->load->model('keys_model');
                    
                    $key['key'] = md5(uniqid($this->input->post('email', true), true));
                    $key['level'] = 2;
                    $key['ignore_limits'] = 0;
                    $key_saved = $this->keys_model->save($key);
                    
                    $item['api_key'] = $key_saved->id;
                }
                
                if ($this->input->post('password'))
                    $item['password'] = $this->input->post('password', true);
                
                $item['first_name'] = $this->input->post('first_name', true);
                $item['last_name'] = $this->input->post('last_name', true);
                $item['email'] = $this->input->post('email', true);
                $item['active'] = $this->input->post('active', true);
                
                if ($saved_user = $this->object_model->save($item)) {
                    if (! isset($this->users_groups_model))
                        $this->load->model('users_groups_model');
                    
                    $setting = array();
                    $user_group = array();
                    
                    if ($rid) {
                        if ($user)
                            $user_group['rid'] = $user[0]->users_groups_rid;
                        
                        if ($user)
                            $setting['rid'] = $user[0]->settings_rid;
                    }
                    
                    $user_group['user_id'] = $saved_user->id;
                    $user_group['group_id'] = $this->input->post('group_id', true);
                    $this->users_groups_model->save($user_group);
                    
                    if (! isset($this->settings_model))
                        $this->load->model('settings_model');
                    
                    $setting['user_id'] = $saved_user->id;
                    $currency_symbol = $this->input->post('currency_symbol', true);
                    $address = trim($this->input->post('address1', true)) . '|#' . trim($this->input->post('address2', true)) . '|#' . trim($this->input->post('city', true)) . '|#' . trim($this->input->post('state', true)) . '|#' . trim($this->input->post('postcode', true)) . '|#' . trim($this->input->post('country', true));
                    $setting['content'] = json_encode(array(
                        'currency_symbol' => $currency_symbol,
                        'invoice_number' => 0,
                        'estimate_number' => 0,
                        'address' => $address
                    ));
                    $this->settings_model->save($setting);
                    
                    redirect(base_url().$this->controller . '/' . "index", 'location');
                } else {
                    $this->data['error'] = $this->object_model->errors();
                    $this->object_model->clear_errors();
                }
            } else {
                $this->data['error'] = validation_errors();
            }
        }
        
        if (! isset($this->groups_model))
            $this->load->model('groups_model');
        $this->data['groups'] = $this->groups_model->get_for_dropdown('groups', array(
            'value' => 'id',
            'name' => 'name'
        ), false, false, false, 'name asc');
        
        $this->template();
    }

    /**
     *
     * settings
     *
     * This method deals with changing the settings information of the currently logged in user
     *
     */
    public function settings()
    {
        $this->object_model->db->join('settings', 'settings.user_id = users.id');
        $user = $this->object_model->get(array(
            'users.id' => $this->live_user->user_id
        ), 'users.*, settings.content, settings.rid as settings_rid');
   
        if ($user && isset($user[0])) {
            $content = json_decode($user[0]->content);
            
            $user[0]->currency_symbol = $content->currency_symbol;
    
            $address = explode('|#', $content->address);
    
            $user[0]->address1 = $address[0];
            $user[0]->address2 = $address[1];
            $user[0]->city = $address[2];
            $user[0]->state = $address[3];
            $user[0]->postcode = $address[4];
            $user[0]->country = $address[5];
    
            if (!empty($content->logo_path)) $user[0]->logo_path = $content->logo_path;
            
            $this->data['result'] = $user[0];
        } else {
            // If no record found redirect to index page
            redirect(base_url().'auth', 'refresh');
        }
    
        if (isset($_POST['fn']) and $_POST['fn'] == md5($this->controller . $this->method)) {
            $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
            $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
            $this->form_validation->set_rules('email', lang('email'), 'required');

            if ($this->form_validation->run() == true) {
                $item = array();
    
                $item['rid'] = $user[0]->rid;
                $item['first_name'] = $this->input->post('first_name', true);
                $item['last_name'] = $this->input->post('last_name', true);
                $item['email'] = $this->input->post('email', true);
                if ($this->input->post('password'))
                    $item['password'] = $this->input->post('password', true);
    
                if ($saved_user = $this->object_model->save($item)) {
                    if (! isset($this->settings_model)) $this->load->model('settings_model');
    
                    $content = json_decode($user[0]->content);
    
                    $setting['rid'] = $user[0]->settings_rid;
                    $setting['user_id'] = $user[0]->id;
                    $content->currency_symbol = $this->input->post('currency_symbol', true);
                    $content->address = trim($this->input->post('address1', true)) . '|#' . trim($this->input->post('address2', true)) . '|#' . trim($this->input->post('city', true)) . '|#' . trim($this->input->post('state', true)) . '|#' . trim($this->input->post('postcode', true)) . '|#' . trim($this->input->post('country', true));
    
                    //Uploading logo
                    $is_upload_error = false;                    
                    $user_upload_path = APPPATH . '../assets/uploads/'.$user[0]->rid.'/';
                    
                    if ($this->input->post("delete_logo"))
                    {
                        unlink(realpath($user_upload_path.$content->logo_path));
                        $content->logo_path = "";
                    }
                    else if (!empty($_FILES['userfile']['name']))
                    {
                        if (!is_dir($user_upload_path))
                        {
                            mkdir($user_upload_path);                            
                        }
                              
                        $config['upload_path'] = realpath($user_upload_path);
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        $config['max_size']  = '2064';
                        $config['max_width'] = '180';
                        $config['max_height'] = '80';
                        $config['overwrite'] = true;
                        
                        $path_parts = pathinfo($_FILES['userfile']['name']);
                        
                        $config['file_name'] = "logo.".$path_parts['extension'];                        
                        
                        $this->load->library('upload', $config);
                        
                        if ($this->upload->do_upload())
                        {
                            $data = $this->upload->data();
                            
                            $content->logo_path = $data['file_name'];
                        }
                        else 
                        {
                            $is_upload_error = true;
                            $this->data['error'] = $this->upload->display_errors();
                        }
                    }
                    
                    if (!$is_upload_error)
                    {
                        $setting['content'] = json_encode($content);

                        $this->settings_model->save($setting);
        
                        redirect(base_url().$this->controller . '/' . "settings", 'location');
                    }
                } else {
                    $this->data['error'] = $this->object_model->errors();
                    $this->object_model->clear_errors();
                }
            } else {
                $this->data['error'] = validation_errors();
            }
        }
    
        $this->template();
    }
    
    /**
     * delete
     *
     * Delete the current user
     *
     */
    public function delete($rid = FALSE)
    {
        if (! $rid)
            redirect(base_url().$this->controller, 'refresh');
        
        $item = $this->object_model->load($rid);
        
        if ($item) {
            $this->object_model->delete_where(array(
                'id' => $item->id
            ));
            
            if (! isset($this->keys_model))
                $this->load->model('keys_model');
            $this->keys_model->delete_where(array(
                'id' => $item->api_key
            ));
        }
        
        redirect(base_url().$this->controller, 'refresh');
    }
}

