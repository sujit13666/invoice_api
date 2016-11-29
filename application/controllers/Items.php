<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Items controller
 *
 * All item action using the website is done here
 */
class Items extends MY_Controller
{

    public function __construct()
    {
        $this->need_auth = TRUE;
        
        parent::__construct();
    }

    /**
     *
     * index
     *
     * This will show the item list page
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
        
        if ($this->ion_auth->is_admin()) {
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'items.name' => $search
                ));
                $this->object_model->db->or_like(array(
                    'items.description' => $search
                ));
            }
            
            $this->object_model->db->join('users', 'users.id = items.user_id');
            $items = $this->object_model->get(false, "items.*, CONCAT(first_name, ' ',last_name) as full_name", 'created_on desc', $this->rows_per_page, $page);
            
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'items.name' => $search
                ));
                $this->object_model->db->or_like(array(
                    'items.description' => $search
                ));
            }
            
            $total_rows = $this->object_model->count();
        } else {
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'items.name' => $search
                ));
                $this->object_model->db->or_like(array(
                    'items.description' => $search
                ));
            }
            
            $items = $this->object_model->get(false, false, 'created_on desc', $this->rows_per_page, $page);
            
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'items.name' => $search
                ));
                $this->object_model->db->or_like(array(
                    'items.description' => $search
                ));
            }
            
            $total_rows = $this->object_model->count();
        }
        
        $this->data['result'] = $items;
        
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
     * This method deals with creating and editing a new item.
     *
     * @param string $rid optional This property defines whether it's a new item or existing item. It will be empty for a new item.
     *
     */
    function edit($rid = FALSE)
    {
        $this->load->library(array(
            'form_validation'
        ));
        $data = array();
        
        if ($rid) {
            $item = $this->object_model->load($rid);
            
            if ($item) {
                $this->data['result'] = $item;
            } else {
                // If no record found redirect to index page
                redirect(base_url().$this->controller, 'refresh');
            }
        } else {
            $this->data['title'] = lang($this->controller . '_' . 'new');
        }
        
        if (isset($_POST['fn']) and $_POST['fn'] == md5($this->controller . $this->method)) {
            if ($this->ion_auth->is_admin())
                $this->form_validation->set_rules('user_id', lang('user'), 'required');
            
            $this->form_validation->set_rules('name', lang('item_name'), 'required');
            
            if ($this->form_validation->run() == true) {
                if ($rid)
                    $data['rid'] = $rid;
                
                if ($this->ion_auth->is_admin())
                    $data['user_id'] = $this->input->post('user_id', true);
                else
                    $data['user_id'] = $this->live_user->user_id;
                
                $data['name'] = $this->input->post('name', true);
                $data['description'] = $this->input->post('description', true);
                $data['rate'] = $this->input->post('rate', true);
                
                if ($this->object_model->save($data)) {
                    redirect(base_url().$this->controller . '/' . "index", 'location');
                } else {
                    $this->data['error'] = lang('save_failed');
                }
            } else {
                $this->data['error'] = validation_errors();
            }
        }
        
        if ($this->ion_auth->is_admin()) {
            if (! isset($this->users_model))
                $this->load->model('users_model');
            $this->data['users'] = $this->users_model->get_for_dropdown('users', array(
                'value' => 'id',
                'name' => 'first_name',
                'name2' => 'last_name'
            ), false, false, false, 'first_name asc');
        }
        
        $this->template();
    }

    /**
     *
     * get_autocomplete
     *
     * This method is used for getting a list of clients (item_id => item name) to populate a dropdown
     */
    public function get_autocomplete()
    {
        $data['result'] = $this->json_response($this->json_status['error']);
        
        if ($this->is_ajax()) {
            $term = $this->input->get('term', TRUE);
            $user_id = $this->input->get('user_id', TRUE);
            
            $this->object_model->db->like(array(
                'name' => $term
            ));
            
            if ($user_id)
                $this->object_model->db->where(array(
                    'user_id' => $user_id
                ));
            
            $items = $this->object_model->get(false, "id, name as label, name as value, rate, description");
            
            if (! $items) {
                $items[] = array(
                    'id' => 0,
                    'value' => lang("no_record_found")
                );
            }
            
            $data['result'] = json_encode($items);
        }
        
        $this->template('/ajax', $data, TRUE);
    }
}
