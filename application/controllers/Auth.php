<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Authentication controller
 *
 * All authentication action using the website is done here
 */
class Auth extends MY_Controller
{

    function __construct()
    {
        // Means that you need to be logged in to access this controller
        $this->need_auth = FALSE;
        
        parent::__construct();
        // $this->load->database();
        $this->load->library(array(
            'ion_auth',
            'form_validation'
        ));
        $this->load->helper(array(
            'url',
            'language'
        ));
        
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        
        $this->lang->load('auth');
    }

    /**
     *
     * index
     *      
     * Shows you the login screen     
     */
    function index()
    {
        if ($this->ion_auth->logged_in())
            redirect(base_url()."Invoices", "refresh");
        
        if (isset($_POST['fn']) and $_POST['fn'] == md5($this->controller . $this->method)) {
            $this->form_validation->set_rules('identity', 'Identity', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
            
            if ($this->form_validation->run() == true) {
                $remember = (bool) $this->input->post('remember', TRUE);
                
                if ($this->ion_auth->login($this->input->post('identity', TRUE), $this->input->post('password', TRUE), $remember)) {
                    redirect(base_url().'Invoices', 'refresh');
                } else {
                    $this->data['error'] = $this->ion_auth->errors();
                }
            }
        }
        
        if (validation_errors())
            $this->data['error'] = validation_errors();
        
        $this->data['identity'] = array(
            'name' => 'identity',
            'id' => 'identity',
            'type' => 'text',
            'value' => $this->form_validation->set_value('identity')
        );
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'type' => 'password'
        );
        
        $this->template();
    }

    /**
     *
     * logout
     *      
     * Logout user
     */
    function logout()
    {
        $this->data['title'] = "Logout";
        
        // log the user out
        $logout = $this->ion_auth->logout();
        
        // redirect them to the login page
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect(base_url().'auth', 'refresh');
    }

    /**
     *
     * change_password
     *      
     * Change your password method
     */
    function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        
        if (! $this->ion_auth->logged_in()) {
            redirect(base_url().'auth/login', 'refresh');
        }
        
        $user = $this->ion_auth->user()->row();
        
        if ($this->form_validation->run() == false) {
            // display the form
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            
            $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
            $this->data['old_password'] = array(
                'name' => 'old',
                'id' => 'old',
                'type' => 'password'
            );
            $this->data['new_password'] = array(
                'name' => 'new',
                'id' => 'new',
                'type' => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'
            );
            $this->data['new_password_confirm'] = array(
                'name' => 'new_confirm',
                'id' => 'new_confirm',
                'type' => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'
            );
            $this->data['user_id'] = array(
                'name' => 'user_id',
                'id' => 'user_id',
                'type' => 'hidden',
                'value' => $user->id
            );
            
            // render
            $this->_render_page('auth/change_password', $this->data);
        } else {
            $identity = $this->session->userdata('identity');
            
            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            
            if ($change) {
                // if the password was successfully changed
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect(base_url().'auth/change_password', 'refresh');
            }
        }
    }

    /**
     *
     * forgot_password
     *      
     * Forgotten password method
     */
    function forgot_password()
    {
        // setting validation rules by checking wheather identity is username or email
        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        } else {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        }
        
        if ($this->form_validation->run() == false) {
            $this->data['type'] = $this->config->item('identity', 'ion_auth');
            // setup the input
            $this->data['identity'] = array(
                'name' => 'identity',
                'id' => 'identity'
            );
            
            if ($this->config->item('identity', 'ion_auth') != 'email') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
            } else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }
            
            // set any errors and display the form
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->_render_page('auth/forgot_password', $this->data);
        } else {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))
                ->users()
                ->row();
            
            if (empty($identity)) {
                
                if ($this->config->item('identity', 'ion_auth') != 'email') {
                    $this->ion_auth->set_error('forgot_password_identity_not_found');
                } else {
                    $this->ion_auth->set_error('forgot_password_email_not_found');
                }
                
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect(base_url()."auth/forgot_password", 'refresh');
            }
            
            // run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
            
            if ($forgotten) {
                // if there were no errors
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect(base_url()."auth/login", 'refresh'); // we should display a confirmation page here instead of the login page
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect(base_url()."auth/forgot_password", 'refresh');
            }
        }
    }

    /**
     *
     * reset_password
     *      
     * Reset your password. When you enter your email in the forgotten email, you'll get an email with a link to reset your password
     *      
     * @param string $code
     * required random set of string characters used to validate the request
     */
    public function reset_password($code = NULL)
    {
        if (! $code) {
            show_404();
        }
        
        $user = $this->ion_auth->forgotten_password_check($code);
        
        if ($user) {
            // if the code is valid then display the password reset form
            
            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');
            
            if ($this->form_validation->run() == false) {
                // display the form
                
                // set the flash data error message if there is one
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
                
                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'
                );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;
                
                // render
                $this->_render_page('auth/reset_password', $this->data);
            } else {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {
                    
                    // something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);
                    
                    show_error($this->lang->line('error_csrf'));
                } else {
                    // finally change the password
                    $identity = $user->{$this->config->item('identity', 'ion_auth')};
                    
                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));
                    
                    if ($change) {
                        // if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect(base_url()."auth/login", 'refresh');
                    } else {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect(base_url().'auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        } else {
            // if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect(base_url()."auth/forgot_password", 'refresh');
        }
    }

    
    /**
     * _get_csrf_nonce
     *
     * Get a csrf code
     * 
     */
    function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);
        
        return array(
            $key => $value
        );
    }

    /**
     * _valid_csrf_nonce
     *
     * validate csrf code
     */
    function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE && $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
