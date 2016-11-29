<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base controller
 *
 * All controller (estimates, invoices, items, users etc.) must extend this base controller
 */
class MY_Controller extends CI_Controller
{

    public $data = array();
 // Stores ALL data for outputting to views
    public $controller = FALSE;
 // Stores the current controller name
    public $method = FALSE;
 // Stores the current method name
    public $need_auth;
 // This options determines if a method/controller is accessable for ALL users or ONLY registered user
    public $object_model;
 // keep loaded primary controller's model
    public $live_user;
 // Holds all infos about currently logged in user
    public $rows_per_page = 20;
 // contains limit for get query when getting results
    public $json_status = array(
        'error' => 0,
        'success' => 1,
        'no_data' => 2
    );

    public function __construct()
    {
        parent::__construct();
        
        $this->rows_per_page = ($this->config->item('rows_per_page')) ? $this->config->item('rows_per_page') : 20;
        
        // URL AND CONTROLLER/METHOD
        $this->controller = strtolower($this->router->fetch_class() ? $this->router->fetch_class() : $this->config->item('default_controller'));
        $this->method = strtolower($this->router->fetch_method() ? $this->router->fetch_method() : 'index');
        
        // If uri segment 3 exists and is the pagination selected (i.e. = page) then check if uri 4 exists as well(will be the number) and use it
        $uri_segments = $this->uri->segment_array();
        $uri_segments_flipped = array_flip($this->uri->segment_array());
        
        // MODEL default loading, make sure that file exists
        if (is_file(APPPATH . 'models/' . ucfirst($this->controller) . '_model.php'))
            $this->load->model($this->controller . '_model', 'object_model');
        
        $this->live_user = new stdClass();
        if (isset($this->object_model))
            $this->object_model->live_user = new stdClass();
            
            // all authentication logic comes in here
        if ($this->need_auth || $this->ion_auth->logged_in()) {
            // If method/controller requires authentication, but user is not logged in, send user back to main page.
            if ($this->ion_auth->logged_in()) {
                $this->live_user = (object) $this->session->userdata;
                
                if (isset($this->object_model))
                    $this->object_model->live_user = $this->live_user;
            } else
                redirect(base_url().'auth', 'refresh');
        }
        
        if (!$this->is_ajax())
        {
            $this->data['title'] = lang(strtolower($this->controller . '_' . $this->method));
        }
        
        $this->data['css'][] = "jquery-ui.min.css";
        $this->data['css'][] = "bootstrap.min.css";
        $this->data['css'][] = "sb-admin.css";
        $this->data['css'][] = "font-awesome.min.css";
        $this->data['css'][] = "general.css";
        
        $this->data['js'][] = "jquery.js";
        $this->data['js'][] = "jquery-ui.min.js";
        $this->data['js'][] = "bootstrap.min.js";
        $this->data['js'][] = "general.js";
    }

    /**
     * template
     * This template engine renders the footer/header and content view and calls the template view to display all three views
     * 
     * @param string $view      optional the view name you would like to load from the views folder
     * @param array  $data      optional the array containing the data
     * @param bool   $is_ajax   optional If ajax call, don't load header, footer, but only send back the requested data 
     * 
     * @return string
     */
    public function template($view = FALSE, $data = FALSE, $is_ajax = FALSE)
    {
        if (! $data)
            $data = $this->data;
            
            // If no view was passed then use controller/method to identify the correct view
        if (! $view)
            $view = $this->controller . '/' . $this->method;
        
        if (! $is_ajax) {
            // if (! isset($data['header_menu'])) $data['header_menu'] = $this->load->view('header_menu', $data, TRUE);
            if (! isset($data['header']))
                $data['header'] = $this->load->view('header', $data, TRUE);
            if (! isset($data['header_menu']))
                $data['header_menu'] = $this->load->view('header_menu', $data, TRUE);
            if (! isset($data['footer']))
                $data['footer'] = $this->load->view('footer', $data, TRUE);
            if (! isset($data['content']))
                $data['content'] = $this->load->view($view, $data, TRUE);
            
            $this->load->view('template', $data);
        } else {
            $this->load->view($view, $data);
        }
    }

    /**
     * is_ajax()
     *
     * detect if ajax or json request
     * 
     * @return boolean TRUE/FALSE
     */
    public function is_ajax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
    
    /**
     * json_response
     * 
     * Creates a json response with the passed in data
     * 
     * @param number    optional   $status  error, success or no_data
     * @param array     optional   $val     the array containg data to be send back
     * @param string    optional   $message Message to be included in the response
     * 
     * @return json string
     */
    public function json_response($status = 0, $val = array(), $message = FALSE)
    {
        $success = FALSE;
        
        if ($status == $this->json_status['error']) {
            $message = $message ? $message : lang('json_error');
        } else 
            if ($status == $this->json_status['success']) {
                $message = $message ? $message : lang('json_success');
                $success = TRUE;
            } else 
                if ($status == $this->json_status['no_data']) {
                    $message = $message ? $message : lang('no_data');
                } else {
                    $message = $message ? $message : lang('no_data');
                }
        
        return json_encode(array(
            'success' => $success,
            'status' => $status,
            'message' => $message,
            'result' => $val
        ));
    }

    /**
     * delete
     *
     * This delete method is used by all controllers that don't implement their own delete method.
     * It let's you delete a single record.
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
        }
        
        redirect(base_url().$this->controller, 'refresh');
    }
}
