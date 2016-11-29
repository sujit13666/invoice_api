<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RESTful clients
 *
 * Retrieve, delete, create and update clients through the RESTful api *
 * http://yourdomain.com/index.php/api/clients
 */
class Clients extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        if (!isset($this->clients_model))
            $this->load->model('clients_model');
    }

    /**
     * create
     *
     * url: http://yourdomain.com/index.php/api/clients/create
     * type: POST
     *
     * Create a new client using RESTful API
     *
     * @global string HTTP_X_API_KEY                required    api key of the current logged in user
     *
     * @param int $user_id required    user_id of the person who is making the request
     * @param string $name required    client name
     * @param int $id optional    client id
     * @param string $email optional
     * @param string $address1 optional
     * @param string $address2 optional
     * @param string $city optional
     * @param string $state optional
     * @param string $postcode optional
     * @param string $country optional
     *
     * @return json response
     */
    public function create_post()
    {
        $rid = $this->post('rid', true);
        $userId = $this->post('userId', true);
        $businessName = $this->post('businessName', true);
        $email = $this->post('email', true);
        $billingAddress = $this->post('billingAddress', true);
        $contactName = $this->post('contactName', true);
        $contactBusinessPhone = $this->post('contactBusinessPhone', true);
        $contactMobile = $this->post('contactMobile', true);
        $contactEmail = $this->post('contactEmail', true);
        $fax = $this->post('fax', true);
        $pager = $this->post('pager', true);
        $website = $this->post('website', true);
        $shippingName = $this->post('shippingName', true);
        $shippingAddress = $this->post('shippingAddress', true);
        $terms = $this->post('terms', true);
        $territory = $this->post('territory', true);
        $taxNumber = $this->post('taxNumber', true);
        $other = $this->post('other', true);
        $note = $this->post('note', true);
        $mapAddress = $this->post('mapAddress', true);
        $latitude = $this->post('latitude', true);
        $longitude = $this->post('longitude', true);


        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );

        if (!$businessName || !$userId || $userId == 0) {
            $response['message'] = lang("client_name_user_id_required");

            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($rid) {
                $client['rid'] = $rid;
            }


            $client['user_id'] = $userId;
            $client['business_name'] = $businessName;
            $client['email'] = $email;
            $client['billing_address'] = $billingAddress;
            $client['contact_name'] = $contactName;
            $client['contact_business_phone'] = $contactBusinessPhone;
            $client['contact_mobile'] = $contactMobile;
            $client['contact_email'] = $contactEmail;
            $client['fax'] = $fax;
            $client['pager'] = $pager;
            $client['website'] = $website;
            $client['shipping_name'] = $shippingName;
            $client['shipping_address'] = $shippingAddress;
            $client['terms'] = $terms;
            $client['territory'] = $territory;
            $client['tax_no'] = $taxNumber;
            $client['other'] = $other;
            $client['note'] = $note;
            $client['map_address'] = $mapAddress;
            $client['latitude'] = $latitude;
            $client['longitude'] = $longitude;

            if ($client = $this->clients_model->save($client)) {
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
     * url:  http://yourdomain.com/index.php/api/clients/get
     * type: POST
     *
     * Retrieve clients with optional additional information
     *
     * @global string HTTP_X_API_KEY                required    api key of the current logged in user
     *
     * @param int $user_id required    user_id of the person who is making the request
     * @param int $include_invoice_lines optional    invoice_id, will return all invoice lines for a specific invoice
     * @param int    include_estimate_lines         optional    estimate_id, will return all estimate lines for a specific estimate
     * @param string include_invoice_number         optional    any string, means it returns back the next invoice number (#) for this user
     * @param string include_estimate_number        optional    any string, means it returns back the next estimate number (#) for this user
     *
     * @return json object
     */
    public function get_post()
    {
        $user_id = $this->post('user_id', true);
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        $include_invoice_lines = $this->post('include_invoice_lines', true);
        $include_estimate_lines = $this->post('include_estimate_lines', true);

        $include_invoice_number = $this->post('include_invoice_number', true);
        $include_estimate_number = $this->post('include_estimate_number', true);
        $include_logo = $this->post('include_logo', true);

        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );

        if (!$user_id || $user_id == 0 || !$api_key) {
            $response['message'] = lang("text_rest_invalid_credentials");

            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->clients_model->db->join('invoices', 'invoices.client_id = clients.id', 'left');
            $this->clients_model->db->join('invoices_lines', 'invoices_lines.invoice_id = invoices.id', 'left');
            $this->clients_model->db->join('users', 'users.id = clients.user_id', 'left');
            $this->clients_model->db->join('keys', 'keys.id = users.api_key', 'left');
            $this->clients_model->db->group_by('clients.id');

            $select = 'clients.*, SUM( IF(invoices.is_paid = 1, (((rate * quantity) * tax_rate) / 100) + (rate * quantity), 0) )  AS total';

            $clients = $this->clients_model->get(array(
                'clients.user_id' => $user_id,
                'keys.key' => $api_key
            ), $select, 'clients.name asc');

            $response['message'] = "";
            $response['success'] = true;

            $response['data']['clients'] = $clients;
            $response['data']['invoice_lines'] = array();
            $response['data']['estimate_lines'] = array();

            if ($include_invoice_lines && $include_invoice_lines > 0) {
                if (!isset($this->invoices_lines_model))
                    $this->load->model('invoices_lines_model');
                $this->invoices_lines_model->db->join('users', 'users.id = invoices_lines.user_id', 'left');
                $this->invoices_lines_model->db->join('keys', 'keys.id = users.api_key', 'left');
                $response['data']['invoice_lines'] = $this->invoices_lines_model->get(array(
                    'invoice_id' => $include_invoice_lines,
                    'invoices_lines.user_id' => $user_id,
                    'keys.key' => $api_key
                ), "invoices_lines.*", "invoices_lines.created_on asc");
            }

            if ($include_estimate_lines && $include_estimate_lines > 0) {
                if (!isset($this->estimates_lines_model))
                    $this->load->model('estimates_lines_model');
                $this->estimates_lines_model->db->join('users', 'users.id = estimates_lines.user_id', 'left');
                $this->estimates_lines_model->db->join('keys', 'keys.id = users.api_key', 'left');
                $response['data']['estimate_lines'] = $this->estimates_lines_model->get(array(
                    'estimate_id' => $include_estimate_lines,
                    'estimates_lines.user_id' => $user_id,
                    'keys.key' => $api_key
                ), "estimates_lines.*", "estimates_lines.created_on asc");
            }

            if ($include_invoice_number || $include_estimate_number || $include_logo) {

                if (!isset($this->settings_model))
                    $this->load->model('settings_model');

                $this->settings_model->db->join('users', 'users.id = settings.user_id', 'left');
                $this->settings_model->db->join('keys', 'keys.id = users.api_key', 'left');

                $settings = $this->settings_model->get(array(
                    'settings.user_id' => $user_id,
                    'keys.key' => $api_key
                ), 'settings.*, users.rid as user_rid', null, 1);

                $response['data']['invoice_number'] = 0;
                $response['data']['estimate_number'] = 0;
                $response['data']['logo'] = '';

                if ($settings) {
                    $content = json_decode($settings[0]->content);

                    if ($include_invoice_number) {
                        $response['data']['invoice_number'] = $content->invoice_number + 1;
                    }

                    if ($include_estimate_number) {
                        $response['data']['estimate_number'] = $content->estimate_number + 1;
                    }

                    if ($include_logo) {
                        $response['data']['logo'] = empty($content->logo_path) ? "" : base64_encode(file_get_contents(base_url("assets/uploads/" . $settings[0]->user_rid . '/' . $content->logo_path)));
                    }
                }
            }

            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }

    /**
     * delete
     *
     * url: http://yourdomain.com/index.php/api/clients/delete
     * type: POST
     *
     * Delete client
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int $user_id required    user_id of the person who is making the request
     * @param int $id required    client id
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

        if (!$user_id || $user_id == 0 || !$api_key) {
            $message['message'] = lang("text_rest_invalid_credentials");

            $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        } else
            if (!$id) {
                $message['message'] = lang("client_required");

                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if ($this->clients_model->delete($api_key, $user_id, $id)) {
                    $this->set_response($message, REST_Controller::HTTP_OK);
                } else {
                    $message['message'] = lang("request_failed");
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            }
    }


    /**
     * get_clients
     *
     * url: http://yourdomain.com/index.php/api/clients/get_client
     * type: POST
     *
     * Delete client
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int $user_id required    user_id of the person who is making the request
     *
     * @return json object
     */
    public function get_clients_post()
    {

        $user_id = $this->post('userId');

        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );

        if (!$user_id) {
            $response['message'] = lang("text_rest_invalid_credentials");
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $select ='clients.id clientId,clients.business_name businessName';
            $clients = $this->clients_model->get(array(
                'clients.user_id' => $user_id),$select);
            $data = $clients;

            $response['success'] = true;
            $response['message'] = 'Client list';
            $response['data']['clients'] = $data;
            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }


    /**
     * delete_clients
     *
     * url: http://yourdomain.com/index.php/api/clients/delete
     * type: POST
     *
     * Delete clients
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int $id required    client ids[]
     *
     * @return json object
     */
    public function delete_clients_post()
    {
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        $ids = $this->post('clientIds');
        $response = array(
            'success' => false,
            'message' => '',
        );

        if (!$ids) {
            $response['message'] = lang("text_rest_invalid_credentials");
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->db->where_in('id', $ids);
            $this->db->delete('clients');

            $response['success'] = true;
            $response['message'] = 'Client delete successfully';
            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }


    public function client_detail_post()
    {
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        $id = $this->post('clientId');

        $response = array(
            'success' => false,
            'message' => '',
        );
        if (!$id) {
            $response['message'] = lang("text_rest_invalid_credentials");
            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {

            $select ='clients.business_name businessName,clients.email,clients.billing_address billingAddress,clients.contact_name contactName,clients.contact_business_phone contactBusinessPhone,clients.contact_mobile contactMobile, clients.contact_email contactEmail, clients.fax,
            clients.pager,clients.website,clients.shipping_name shippingName,clients.shipping_address shippingAddress,clients.terms, clients.territory, clients.tax_no taxNo,clients.other,clients.note,clients.map_address mapAddress, clients.latitude, clients.longitude,clients.created_on createdOn';
            $client = $this->clients_model->get(array(
                'clients.id' => $id),$select);
            $data = $client;

            $response['success'] = true;
            $response['message'] = 'Client details';
            $response['data']['clients'] = $data;
            $this->set_response($response, REST_Controller::HTTP_OK);

        }
    }

}
