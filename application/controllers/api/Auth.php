<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once 'application\libraries\swift_mailer\swift_required.php';


/**
 * RESTful authentication
 * http://yourdomain.com/index.php/api/auth
 */
class Auth extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        if (!isset($this->users_model))
            $this->load->model('users_model');

    }

    /**
     * login
     *
     * url:  http://yourdomain.com/index.php/api/auth/login
     * type: POST
     *
     * Used by the API to log you in
     *
     * @param string $email required    email with password base64 encoded base64(peter@email.com:password)
     * @param string $http_auth required    basic authentication type
     *
     * @return json object
     */
    public function login_post()
    {
        // Returns NULL if the SERVER variables PHP_AUTH_USER and HTTP_AUTHENTICATION don't exist
        $email = $this->input->post('email_address');
        $http_auth = $this->input->server('HTTP_AUTHENTICATION');

        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );

        if (empty($email)) {
            $response['message'] = lang("email_is_required");

            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $password = NULL;

            if ($email !== NULL) {
                $password = $this->input->post('password');
            } elseif ($http_auth !== NULL) {
                // If the authentication header is set as basic, then extract the username and password from
                // HTTP_AUTHORIZATION e.g. my_username:my_password.
                if (strpos(strtolower($http_auth), 'basic') === 0) {
                    // Search online for HTTP_AUTHORIZATION workaround to explain what this is doing
                    list ($email, $password) = explode(':', base64_decode(substr($this->input->server('HTTP_AUTHORIZATION'), 6)));
                }
            }

            if (!isset($this->ion_auth_model))
                $this->load->model('ion_auth_model');

            if ($this->ion_auth_model->login($email, $password)) {
                if (!isset($this->users_model))
                    $this->load->model('users_model');
                if (!isset($this->settings_model))
                    $this->load->model('settings_model');

                $this->users_model->db->join('keys', 'keys.id = users.api_key', 'left');
                $this->settings_model->db->join('settings', 'settings.user_id = users.id', 'left');
                $user = $this->users_model->get(array(
                    'email' => $email,
                    'active' => 1
                ), 'users.id,users.email,users.active,users.username as userName,keys.key as api_key, settings.content');

                $response['success'] = TRUE;
                $response['data'] = $user[0];
                $this->set_response($response, REST_Controller::HTTP_ACCEPTED);
            } else {
                $response['message'] = $this->ion_auth_model->api_errors();

                $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
            }
        }
    }

    /**
     * register
     *
     * url:  http://yourdomain.com/index.php/api/auth/register
     * type: POST
     *
     * Used by the API to register a new user
     *
     * @param string $email required
     * @param string $password required
     * @param string $firstname required
     * @param string $lastname required
     *
     * @return json object
     */
    public function register_post()
    {
        $email = $this->post('email', true);
        $password = $this->post('password', true);
//        $firstname = $this->post('firstname', true);
//        $lastname = $this->post('lastname', true);
        $userName = $this->post('userName', true);

        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );

        if (empty($email) || empty($password) || empty($userName)) {
            $response['message'] = lang("register_form_required");

            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else
            if (!(bool)filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = lang("email_is_invalid");

                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if (!isset($this->ion_auth_model))
                    $this->load->model('ion_auth_model');

//                $additional_data['first_name'] = $firstname;
//                $additional_data['last_name'] = $lastname;
                $additional_data['username'] = $userName;

                if ($user = $this->ion_auth_model->register($email, $password, $email, $additional_data)) {
                    if (!isset($this->settings_model))
                        $this->load->model('settings_model');

                    $settings['user_id'] = $user->id;

                    $settings['content'] = json_encode(array(
                        'currency_symbol' => '$',
                        'invoice_number' => 0,
                        'estimate_number' => 0,
                        'address' => '|#|#|#|#|#'
                    ));

                    $this->settings_model->save($settings);

                    $response['message'] = lang("record_creation_successful");
                    $response['success'] = true;

                    $user->content = $settings['content'];
                    $response['data'] = $user;

                    $this->set_response($response, REST_Controller::HTTP_CREATED);
                } else {
                    $response['message'] = $this->ion_auth_model->api_errors();

                    $this->set_response($response, REST_Controller::HTTP_OK);
                }
            }
    }



    /**
     * password_change_email
     *
     * url:  http://yourdomain.com/index.php/api/auth/password_change_email
     * type: POST
     *
     * Used by the API to register a new user
     *
     * @param string $email required
     *
     * @return json object
     */

    public function password_change_email_post()
    {
        $email = $this->post('email', true);

        if (empty($email)) {
            $response['message'] = "email required";
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $select = '*';
            $user = $this->users_model->get(array(
                'email' => $email
            ), $select);
            if (empty($user)) {
                $response['message'] = "Email not found in existing users";
            } else {
                //primary Id of the user.
                $id = $user[0]->id;
                $resetCode = $id . str_shuffle('12345');
                $data = array(
                    'reset_pin' => $resetCode
                );
                $this->db->where('id', $id);
                if ($this->db->update('users', $data)) {
                    //Create the Transport
                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
                        ->setUsername('sayeed.7271470@gmail.com')
                        ->setPassword('sayeed.maniac');
                    //Create the message
                    $message = Swift_Message::newInstance();

                    //Give the message a subject
                    $message->setSubject('Invoice App Reset Password ')
                        ->setFrom('sayeed.7271470@gmail.com')
                        ->setTo($email)
                        ->setBody('Your reset pin for invoice app password change is : ' . $resetCode);

                    //Create the Mailer using your created Transport
                    $mailer = Swift_Mailer::newInstance($transport);
                    //Send the message
                    $result = $mailer->send($message);

                    if ($result) {
                        $response['message'] = "Email send successfully";
                        $response['success'] = true;
                        $this->set_response($response, REST_Controller::HTTP_OK);
                    } else {
                        $response['message'] = "Email send error";
                        $response['success'] = false;
                        $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
                    }

                }
            }
        }
    }

    /**
     * password_change
     *
     * url:  http://yourdomain.com/index.php/api/auth/password_change
     * type: POST
     *
     * Used by the API to register a new user
     *
     * @param string $resetPin required
     * @param string $password required
     * @param string $retypePassword required
     *
     * @return json object
     */

     public function password_change_post()
    {
        $resetPin = $this->post('resetPin', true);
        $password = $this->post('password', true);

        $select = 'email';
        $user = $this->users_model->get(array(
            'reset_pin' => $resetPin
        ), $select);
        $email = $user[0]->email;
        if (empty($email)) {
            $response['message'] = "Invalid Reset Code";
            $response['success'] = false;
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->ion_auth_model->reset_password($email, $password);
            $response['message'] = "Password Change Successful";
            $response['success'] = true;
            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }
}