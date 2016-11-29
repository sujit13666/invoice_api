<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Estimates controller
 *
 * All estimate action using the website is done here
 */
class Estimates extends MY_Controller
{

    public function __construct()
    {
        $this->need_auth = TRUE;
        
        parent::__construct();
        
        if (! isset($this->estimates_lines_model))
            $this->load->model('estimates_lines_model');
    }

    /**
     *
     * index
     *
     * This will show the estimate list page
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
                    'estimates.estimate_number' => $search
                ));
            }
            
            $this->object_model->db->join('estimates_lines', 'estimates_lines.estimate_id = estimates.id', 'left');
            $this->object_model->db->join('clients', 'clients.id = estimates.client_id', 'left');
            $this->object_model->db->join('users', 'users.id = estimates.user_id', 'left');
            $this->object_model->db->group_by('estimates.id');
            
            $select = 'estimates.*, clients.name as client_name, CONCAT(first_name, " ",last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total';
            
            $items = $this->object_model->get(false, $select, 'estimates.created_on desc', $this->rows_per_page, $page);
            
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'estimates.estimate_number' => $search
                ));
            }
            
            $total_rows = $this->object_model->count();
        } else {
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'estimates.estimate_number' => $search
                ));
            }
            
            $this->object_model->db->join('estimates_lines', 'estimates_lines.estimate_id = estimates.id', 'left');
            $this->object_model->db->join('clients', 'clients.id = estimates.client_id', 'left');
            $this->object_model->db->join('users', 'users.id = estimates.user_id', 'left');
            $this->object_model->db->group_by('estimates.id');
            
            $select = 'estimates.*, clients.name as client_name, CONCAT(first_name, " ",last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total';
            
            $items = $this->object_model->get(false, $select, 'estimates.created_on desc', $this->rows_per_page, $page);
            
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'estimates.estimate_number' => $search
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
     * This method deals with creating and editing a new estimate.
     *
     * @param string $rid optional This property defines whether it's a new estimate or existing estimate. It will be empty for a new estimate.
     *
     */
    function edit($rid = FALSE)
    {
        $this->load->library(array(
            'form_validation'
        ));
        $data = array();
        
        if ($rid) {
            $this->object_model->db->join('clients', 'clients.id = estimates.client_id');
            $this->object_model->db->select('estimates.*, clients.name as client');
            
            $item = $this->object_model->get(array(
                'estimates.rid' => $rid
            ));
            
            if ($item) {
                $this->data['result'] = $item[0];
                $this->data['lines'] = $this->estimates_lines_model->get(array(
                    'estimate_id' => $item[0]->id
                ));
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
            
            $this->form_validation->set_rules('estimate_number', lang('estimate_number'), 'required');
            $this->form_validation->set_rules('client_id', lang('client'), 'required');
            $this->form_validation->set_rules('estimate_date', lang('estimate_date'), 'required');
            
            $items = $this->input->post('items[]', TRUE);
            
            if ($this->form_validation->run() == true && count($items) > 1 && $items[1] != '') {
                if ($rid) {
                    $data['id'] = $item[0]->id;
                    
                    $this->estimates_lines_model->delete_where('estimate_id = ' . $item[0]->id);
                }
                
                if ($this->ion_auth->is_admin())
                    $data['user_id'] = $this->input->post('user_id', true);
                else
                    $data['user_id'] = $this->live_user->user_id;
                
                $data['estimate_number'] = $this->input->post('estimate_number', true);
                $data['client_id'] = $this->input->post('client_id', true);
                $data['tax_rate'] = $this->input->post('tax_rate', true);
                $data['notes'] = $this->input->post('notes', true);
                
                $estimate_date = $this->input->post('estimate_date', true);
                if ($estimate_date)
                    $data['estimate_date'] = strtotime($estimate_date);
                
                $due_date = $this->input->post('due_date', true);
                if ($due_date)
                    $data['due_date'] = strtotime($due_date);
                
                $data['is_invoiced'] = $this->input->post('is_invoiced') ? $this->input->post('is_invoiced', true) : 0;
                
                if ($estimate_saved = $this->object_model->save($data)) {
                    if (! isset($this->settings_model))
                        $this->load->model('settings_model');
                    $this->settings_model->update(array(
                        'setting_name' => 'estimate_number',
                        'setting_value' => $data['estimate_number'],
                        'user_id' => $data['user_id']
                    ));
                    
                    $descriptions = $this->input->post('descriptions[]', TRUE);
                    $rates = $this->input->post('rates[]', TRUE);
                    $quantities = $this->input->post('quantities[]', TRUE);
                    
                    if (count($items) > 0) {
                        for ($i = 1; $i < count($items); $i ++) {
                            if ($items[$i] == '')
                                continue;
                            
                            $line = array();
                            $line['user_id'] = $data['user_id'];
                            $line['estimate_id'] = $estimate_saved->id;
                            $line['name'] = $items[$i];
                            $line['rate'] = $rates[$i];
                            $line['quantity'] = $quantities[$i];
                            $line['description'] = $descriptions[$i];
                            
                            $this->estimates_lines_model->save($line);
                        }
                    }
                    
                    redirect(base_url().$this->controller . '/' . "index", 'location');
                } else {
                    $this->data['error'] = lang('save_failed');
                }
            } else {
                if (count($items) <= 1 || count($items) > 1 && $items[1] == '')
                    $this->data['error'] = lang('items_required');
                else
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
}
