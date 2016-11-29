<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Invoices controller
 *
 * All invoices action using the website is done here
 */
class Invoices extends MY_Controller
{

    public function __construct()
    {
        $this->need_auth = TRUE;
        
        parent::__construct();
        
        if (! isset($this->invoices_lines_model))
            $this->load->model('invoices_lines_model');
    }

    /**
     *
     * index
     *
     * This will show the invoice list page
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
                    'invoices.invoice_number' => $search
                ));
            }
            
            $this->object_model->db->join('invoices_lines', 'invoices_lines.invoice_id = invoices.id', 'left');
            $this->object_model->db->join('clients', 'clients.id = invoices.client_id', 'left');
            $this->object_model->db->join('users', 'users.id = invoices.user_id', 'left');
            $this->object_model->db->group_by('invoices.id');
            
            $select = 'invoices.*, clients.name as client_name, CONCAT(first_name, " ",last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total';
            
            $items = $this->object_model->get(false, $select, 'invoices.created_on desc', $this->rows_per_page, $page);
            
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'invoices.invoice_number' => $search
                ));
            }
            
            $total_rows = $this->object_model->count();
        } else {
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'invoices.invoice_number' => $search
                ));
            }
            
            $this->object_model->db->join('invoices_lines', 'invoices_lines.invoice_id = invoices.id', 'left');
            $this->object_model->db->join('clients', 'clients.id = invoices.client_id', 'left');
            $this->object_model->db->join('users', 'users.id = invoices.user_id', 'left');
            $this->object_model->db->group_by('invoices.id');
            
            $select = 'invoices.*, clients.name as client_name, CONCAT(first_name, " ",last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total';
            
            $items = $this->object_model->get(false, $select, 'invoices.created_on desc', $this->rows_per_page, $page);
            
            // Search
            if ($search) {
                $this->object_model->db->like(array(
                    'invoices.invoice_number' => $search
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
     * This method deals with creating and editing a new invoice.
     *
     * @param string $rid optional This property defines whether it's a new invoice or existing invoice. It will be empty for a new invoice.
     *
     */
    function edit($rid = FALSE)
    {
        $this->load->library(array(
            'form_validation'
        ));
        $data = array();
        
        if ($rid) {
            $this->object_model->db->join('clients', 'clients.id = invoices.client_id');
            $this->object_model->db->select('invoices.*, clients.name as client');
            
            $item = $this->object_model->get(array(
                'invoices.rid' => $rid
            ));
            
            if ($item) {
                $this->data['result'] = $item[0];
                $this->data['lines'] = $this->invoices_lines_model->get(array(
                    'invoice_id' => $item[0]->id
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
            
            $this->form_validation->set_rules('invoice_number', lang('invoice_number'), 'required');
            $this->form_validation->set_rules('client_id', lang('client'), 'required');
            $this->form_validation->set_rules('invoice_date', lang('invoice_date'), 'required');
            
            $items = $this->input->post('items[]', TRUE);
            
            if ($this->form_validation->run() == true && count($items) > 1 && $items[1] != '') {
                if ($rid) {
                    $data['id'] = $item[0]->id;
                    
                    $this->invoices_lines_model->delete_where('invoice_id = ' . $item[0]->id);
                }
                
                if ($this->ion_auth->is_admin())
                    $data['user_id'] = $this->input->post('user_id', true);
                else
                    $data['user_id'] = $this->live_user->user_id;
                
                $data['invoice_number'] = $this->input->post('invoice_number', true);
                $data['client_id'] = $this->input->post('client_id', true);
                $data['tax_rate'] = $this->input->post('tax_rate', true);
                $data['notes'] = $this->input->post('notes', true);
                
                $invoice_date = $this->input->post('invoice_date', true);
                if ($invoice_date)
                    $data['invoice_date'] = strtotime($invoice_date);
                
                $due_date = $this->input->post('due_date', true);
                if ($due_date)
                    $data['due_date'] = strtotime($due_date);
                
                $data['is_paid'] = $this->input->post('is_paid') ? $this->input->post('is_paid', true) : 0;
                
                if ($invoice_saved = $this->object_model->save($data)) {
                    if (! isset($this->settings_model))
                        $this->load->model('settings_model');
                    $this->settings_model->update(array(
                        'setting_name' => 'invoice_number',
                        'setting_value' => $data['invoice_number'],
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
                            $line['invoice_id'] = $invoice_saved->id;
                            $line['name'] = $items[$i];
                            $line['rate'] = $rates[$i];
                            $line['quantity'] = $quantities[$i];
                            $line['description'] = $descriptions[$i];
                            
                            $this->invoices_lines_model->save($line);
                        }
                    }
                    
                    redirect(base_url().$this->controller . '/' . "index", 'location');
                } else {
                    $this->data['error'] = lang('save_failed');
                }
            } else {
            	if (validation_errors())
            		$this->data['error'] = validation_errors();
                else if (count($items) <= 1 || count($items) > 1 && $items[1] == '')
                    $this->data['error'] = lang('items_required');                    
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
