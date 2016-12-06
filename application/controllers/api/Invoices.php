<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RESTful Invoices
 *
 * Retrieve, delete, create and update invoices through the RESTful api *
 * http://yourdomain.com/index.php/api/invoices
 */
class Invoices extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        if (!isset($this->invoices_model))
            $this->load->model('invoices_model');
        if (!isset($this->invoices_lines_model))
            $this->load->model('invoices_lines_model');
        if (!isset($this->invoice_items_model))
            $this->load->model('invoice_items_model');
    }

    /**
     * create
     *
     * url:  http://yourdomain.com/index.php/api/invoices/create
     * type: POST
     *
     * Create a new invoice using RESTful API
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int $user_id required     user_id of the person who is making the request
     * @param int $invoice_number required     invoice number #
     * @param int $id optional     invoice id
     * @param int $invoice_date optional     unixtime
     * @param int $due_date optional     unixtime
     * @param int $client_id optional
     * @param int $paid optional     0 or 1, marks invoice as paid
     * @param float $tax_rate optional     percentage value from 0 - 100
     * @param string $notes optional     Any free form text, estimate note
     * @param array $items required     list of items ('Name'=>'item1','Description'=>'item description','Rate'=>25.00,'Quantity'=>5)
     *
     * @return json object
     */
    public function create_post()
    {
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;
        $id = $this->post('id', true);
        $user_id = $this->post('user_id', true);
        $invoice_number = $this->post('invoice_number', true);
        $tax_rate = $this->post('tax_rate', true);

        $invoice_date = $this->post('invoice_date', true);
        $due_date = $this->post('due_date', true);
        $client_id = $this->post('client_id', true);
        $notes = $this->post('notes', true);
        $paid = $this->post('paid', true);

        $items = json_decode($this->post('items', true));

        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );

        if (!$invoice_number || !$user_id || $user_id == 0) {
            $response['message'] = lang("invoice_number_user_id_required");

            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($id)
                $invoice['id'] = $id;

            $invoice['user_id'] = $user_id;
            $invoice['invoice_number'] = $invoice_number;
            $invoice['tax_rate'] = $tax_rate;

            $invoice['invoice_date'] = $invoice_date;
            $invoice['due_date'] = $due_date;
            $invoice['client_id'] = $client_id;
            $invoice['notes'] = $notes;
            $invoice['is_paid'] = $paid;

            if ($invoice = $this->invoices_model->save($invoice)) {
                if ($id) {
                    $this->invoices_lines_model->delete_where('invoice_id = ' . $invoice->id);
                } else {
                    // If new record update invoice number in settings
                    if (!isset($this->settings_model))
                        $this->load->model('settings_model');

                    $this->settings_model->db->join('users', 'users.id = settings.user_id', 'left');
                    $this->settings_model->db->join('keys', 'keys.id = users.api_key', 'left');
                    $settings = $this->settings_model->get(array(
                        'settings.user_id' => $user_id,
                        'keys.key' => $api_key
                    ), "settings.*", null, 1);

                    if ($settings) {
                        $array_settings = (array)$settings[0];

                        $content = (array)json_decode($array_settings['content']);

                        $content['invoice_number'] += 1;

                        $array_settings['content'] = json_encode($content);

                        $this->settings_model->save($array_settings);
                    }
                }

                foreach ($items as $key => $item) {
                    $invoice_item = [];

                    $invoice_item['user_id'] = $user_id;
                    $invoice_item['invoice_id'] = $invoice->id;
                    $invoice_item['name'] = $item->Name;
                    $invoice_item['description'] = $item->Description;
                    $invoice_item['rate'] = $item->Rate;
                    $invoice_item['quantity'] = $item->Quantity;

                    $this->invoices_lines_model->save($invoice_item);
                }

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
     * url:  http://yourdomain.com/index.php/api/invoices/get
     * type: POST
     *
     * Retrieve invoices for a specific user
     *
     * @global string HTTP_X_API_KEY                required    api key of the current logged in user
     *
     * @param int $user_id required    user_id of the person who is making the request
     *
     * @return json object
     */
    public function get_post()
    {
        $user_id = $this->post('user_id', true);
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : false;

        $response = array(
            'success' => false,
            'message' => '',
            'data' => ''
        );

        if (!$user_id || $user_id == 0 || !$api_key) {
            $response['message'] = lang("text_rest_invalid_credentials");

            $this->set_response($response, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->invoices_model->db->join('invoices_lines', 'invoices_lines.invoice_id = invoices.id', 'left');
            $this->invoices_model->db->join('clients', 'clients.id = invoices.client_id', 'left');
            $this->invoices_model->db->join('users', 'users.id = invoices.user_id', 'left');
            $this->invoices_model->db->join('keys', 'keys.id = users.api_key', 'left');
            $this->invoices_model->db->group_by('invoices.id');

            $select = 'invoices.*, clients.name as client_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total';

            $invoices = $this->invoices_model->get(array(
                'invoices.user_id' => $user_id,
                'keys.key' => $api_key
            ), $select, 'invoice_date desc, created_on desc');

            $response['message'] = "";
            $response['success'] = true;

            $response['data']['invoices'] = $invoices;

            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }

    /**
     * delete
     *
     * url:  http://yourdomain.com/index.php/api/invoices/delete
     * type: POST
     *
     * Delete invoice
     *
     * @global string HTTP_X_API_KEY    required    api key of the current logged in user
     *
     * @param int $user_id required    user_id of the person who is making the request
     * @param int $id required    invoice id
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
                $message['message'] = lang("invoice_required");

                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if ($this->invoices_model->delete($api_key, $user_id, $id)) {
                    $this->set_response($message, REST_Controller::HTTP_OK);
                } else {
                    $message['message'] = lang("request_failed");
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            }
    }

    public function create_invoice_post()
    {
        $userId = $this->post('userId', true);
        $clientId = $this->post('clientId', true);

        // Custom function written in Invoices_model
        // To fetch the last entry of a specific user
        $maxInvoiceNumber = $this->invoices_model->get_max_invoice($userId);
        $invoiceNumber = $maxInvoiceNumber[0]->invoice_number + 1;
        $invoiceDate = date("Y-m-d");
        $dueDate = date('Y-m-d', strtotime('+1 month', strtotime($invoiceDate)));
        $subtotal = $this->post('subtotal', true);
        $vat = $this->post('vat', true);
        $total = $subtotal + $vat;
        $paid = $this->post('paid', true);
        $comment = $this->post('comment', true);
        $isPaid = $this->post('isPaid', true);
        $cardAcceptable = $this->post('cardAcceptable', true);

        //Image Upload Procedure
        $config['upload_path'] = './images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $ext = pathinfo($_FILES["userfile"]['name'], PATHINFO_EXTENSION);
        $new_name = $userId . '_' . time() . str_shuffle('12345abcde') . '.' . $ext;
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
        $attachedImage = $config['file_name'];


        if (!$userId || $userId == 0) {
            $response['message'] = lang("invoice_number_user_id_required");
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $invoice['user_id'] = $userId;
            $invoice['client_id'] = $clientId;
            $invoice['invoice_number'] = $invoiceNumber;
            $invoice['invoice_date'] = $invoiceDate;
            $invoice['due_date'] = $dueDate;
            $invoice['subtotal'] = $subtotal;
            $invoice['vat'] = $vat;
            $invoice['total'] = $total;
            $invoice['paid'] = $paid;
            $invoice['comment'] = $comment;
            $invoice['is_paid'] = $isPaid;
            $invoice['card_acceptable'] = $cardAcceptable;
            $invoice['attached_image'] = $attachedImage;
            $invoice['is_invoice'] = 1;

            $invoice = $this->invoices_model->save($invoice);

        }

        $invoiceId = $invoice->id;
        $itemIds = $this->post('itemId', true);
        $qtys = $this->post('qty', true);


        $this->insert_invoice_item($invoiceId, $itemIds, $qtys);

    }


    private function insert_invoice_item($invoiceId, $itemIds, $qtys)
    {
        $successFlag = 0;
        $unsuccessFlag = 0;
        foreach ($itemIds as $key => $val) {
            $invoice_item['invoice_id'] = $invoiceId;
            $invoice_item['item_id'] = $val;
            $invoice_item['qty'] = $qtys[$key];
            if ($this->invoice_items_model->save($invoice_item)) {
                $successFlag += 1;
            } else {
                $unsuccessFlag += 1;
            }
        }
        if ($unsuccessFlag < 1) {
            $response['message'] = "Record successful";
            $response['success'] = true;
            $this->set_response($response, REST_Controller::HTTP_CREATED);
        } else {
            $response['message'] = "Record unsuccessful";
            $response['success'] = false;
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

    }


    // Invoice List return , argument (userId)
    public function invoice_list_post($userId = false)
    {
        $userId = $this->post('userId', true);

        if (!$userId || $userId == 0) {
            $message['message'] = "User Id not provided.";

            $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        } else {

            $select = 'id,invoice_date,due_date ';

            $invoices = $this->invoices_model->get(array(
                'invoices.user_id' => $userId,
                'invoices.is_invoice'=>1
//                'keys.key' => $api_key
            ), $select, 'invoice_date desc, created_on desc');

            $response['message'] = "";
            $response['success'] = true;
            $response['data']['invoices'] = $invoices;
            $this->set_response($response, REST_Controller::HTTP_OK);

        }
    }


    // delete invoice primary id
    public function delete_invoice_post()
    {
        $id = $this->post('id', true);
        $message = array(
            'success' => false,
            'message' => ''
        );
        if (!$id || $id == 0) {
            $message['message'] = "Id not provided.";
            $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->db->where('invoice_id', $id);
            $data = $this->db->delete('invoice_items');
//            = $this->db->affected_rows();
            if ($data) {
                $this->db->where('id', $id);
                if ($this->db->delete('invoices')) {
                    $message['success'] = true;
                    $message['message'] = "request successful";
                    $this->set_response($message, REST_Controller::HTTP_OK);
                } else {
                    $message['message'] = "request failed";
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            }

        }
    }

    /******* QUOTE *******/


    /**
     * create QUOTE
     *
     * url:  http://yourdomain.com/index.php/api/invoices/quote_create
     * type: POST
     *
     * Create a new quote using RESTful API
     *
     * @return json object
     */
    public function quote_create_post()
    {

        $userId = $this->post('userId', true);
        $clientId = $this->post('clientId', true);

        // Custom function written in Invoices_model
        // To fetch the last entry of a specific user
        $maxInvoiceNumber = $this->invoices_model->get_max_invoice($userId);
        $invoiceNumber = $maxInvoiceNumber[0]->invoice_number + 1;
        $invoiceDate = date("Y-m-d");
        $dueDate = date('Y-m-d', strtotime('+1 month', strtotime($invoiceDate)));
        $subtotal = $this->post('subtotal', true);
        $vat = $this->post('vat', true);
        $total = $subtotal + $vat;
        $paid = $this->post('paid', true);
        $comment = $this->post('comment', true);
        $isPaid = $this->post('isPaid', true);
        $cardAcceptable = $this->post('cardAcceptable', true);
        $isInvoice = $this->post('isInvoice', true);

        if (!$userId || $userId == 0) {
            $response['message'] = "invoice_number_user_id_required";
            $response['status'] = false;
            $this->set_response($response, REST_Controller::HTTP_OK);
        } else {
            $invoice['user_id'] = $userId;
            $invoice['client_id'] = $clientId;
            $invoice['invoice_number'] = $invoiceNumber;
            $invoice['invoice_date'] = $invoiceDate;
            $invoice['due_date'] = $dueDate;
            $invoice['subtotal'] = $subtotal;
            $invoice['vat'] = $vat;
            $invoice['total'] = $total;
            $invoice['paid'] = $paid;
            $invoice['comment'] = $comment;
            $invoice['is_paid'] = 0;
            $invoice['card_acceptable'] = $cardAcceptable;
//            $invoice['attached_image'] = $attachedImage;
            $invoice['is_invoice'] = $isInvoice;

            $invoice = $this->invoices_model->save($invoice);

            $invoiceId = $invoice->id;
            $itemIds = $this->post('itemId', true);
            $qtys = $this->post('qty', true);


            $this->insert_invoice_item($invoiceId, $itemIds, $qtys);

        }
    }


    /**
     * get quotes by userId
     *
     * url:  http://yourdomain.com/index.php/api/invoices/quote_list
     * type: POST
     *
     * Create a new quote using RESTful API
     *
     * @return json object
     */

    public function quote_list_post()
    {
        $userId = $this->post('userId', true);

        if (!$userId || $userId == 0) {
            $message['message'] = "User Id not provided.";

            $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        } else {

            $select = 'id,invoice_date,due_date ';

            $quotes = $this->invoices_model->get(array(
                'invoices.user_id' => $userId,
                'invoices.is_invoice'=>0
//                'keys.key' => $api_key
            ), $select, 'invoice_date desc, created_on desc');

            $response['message'] = "";
            $response['success'] = true;
            $response['data']['quotes'] = $quotes;
            $this->set_response($response, REST_Controller::HTTP_OK);

        }
    }

    /**
     * get quote by Id
     *
     * url:  http://yourdomain.com/index.php/api/invoices/get_quote
     * type: POST
     *
     *
     * @return json object
     */

    public function get_quote_post()
    {
        $id = $this->post('id', true);

        if (!$id || $id == 0) {
            $message['message'] = "Id not provided.";
            $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $select = '*';
            $quote = $this->invoices_model->get(array(
                'invoices.id' => $id,
                'invoices.is_invoice' => 0
            ),$select);

            $response['message'] = "";
            $response['success'] = true;
            $response['data']['quote'] = $quote;
            $this->set_response($response, REST_Controller::HTTP_OK);
        }
    }

    // delete quote by primary id
    public function delete_quote_post()
    {
        $id = $this->post('id', true);
        $message = array(
            'success' => false,
            'message' => ''
        );
        if (!$id || $id == 0) {
            $message['message'] = "Id not provided.";
            $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        } else {
            $this->db->where('invoice_id', $id);
            $data = $this->db->delete('invoice_items');

            if ($data) {
                $this->db->where('id', $id);
                if ($this->db->delete('invoices')) {
                    $message['success'] = true;
                    $message['message'] = "request successful";
                    $this->set_response($message, REST_Controller::HTTP_OK);
                } else {
                    $message['message'] = "request failed";
                    $message['success'] = true;
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            }

        }
    }

    /*Generate Sales PDF*/

    public function generate_sales_pdf_post()
    {

        $this->load->helper(array('dompdf', 'file'));

        $userId = $this->post('userId', true);
        $dateFrom = $this->post('dateFrom', true);
        $dateTo = $this->post('dateTo', true);
        $sortType = $this->post('sortType', true);

        $data = array();

        if ($sortType == "customer") {
            $customers = $this->invoices_model->getDistinctCustomersForPDF($userId, $dateFrom, $dateTo);
            foreach ($customers as $key => $customer) {
                $customers[$key]->invoices = $this->invoices_model->getFilteredInvoicesByCustomerForPDF($userId, $dateFrom, $dateTo, $customer);
            }
            $data = $customers;

        }  elseif ($sortType == "date") {
            $dates = $this->invoices_model->getDistinctDatesForPDF($userId, $dateFrom, $dateTo);
            foreach ($dates as $key => $date) {
                $dates[$key]->invoices = $this->invoices_model->getFilteredInvoicesByDateForPDF($userId, $dateFrom, $dateTo, $date);
            }
            $data = $dates;
        } else {

            $response = array();
            $response['message'] = "";
            $response['success'] = false;

            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        }


        $html = $this->load->view('/pdf_templates/sales_invoice.php', array(
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortType' => $sortType
        ), true);

        $output = pdf_create($html);
        $filePath = '/pdf/invoices/sales_';
        $fileName = date("d-M-Y-H-i-s") . rand(0, 90000) . '.pdf';

        file_put_contents('./pdf/invoices/sales_' . $fileName, $output);

        $response = array();
        $response['message'] = "";
        $response['success'] = true;
        $response['data']['filePath'] = $filePath . $fileName;

        $this->set_response($response, REST_Controller::HTTP_OK);

    }

    /*Generate Payments PDF*/

    public function generate_payments_pdf_post()
    {

        $this->load->helper(array('dompdf', 'file'));

        $userId = $this->post('userId', true);
        $dateFrom = $this->post('dateFrom', true);
        $dateTo = $this->post('dateTo', true);
        $sortType = $this->post('sortType', true);

        $data = array();

        if ($sortType == "customer") {
            $customers = $this->invoices_model->getDistinctCustomersForPaymentPDF($userId, $dateFrom, $dateTo);
            foreach ($customers as $key => $customer) {
                $customers[$key]->invoices = $this->invoices_model->getFilteredInvoicesByCustomerForPaymentPDF($userId, $dateFrom, $dateTo, $customer);
            }
            $data = $customers;

        }  elseif ($sortType == "date") {
            $dates = $this->invoices_model->getDistinctDatesForPaymentPDF($userId, $dateFrom, $dateTo);
            foreach ($dates as $key => $date) {
                $dates[$key]->invoices = $this->invoices_model->getFilteredInvoicesByDateForPaymentPDF($userId, $dateFrom, $dateTo, $date);
            }
            $data = $dates;
        } else {

            $response = array();
            $response['message'] = "";
            $response['success'] = false;

            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        }


        $html = $this->load->view('/pdf_templates/payment_invoice.php', array(
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortType' => $sortType
        ), true);

        $output = pdf_create($html);
        $filePath = '/pdf/invoices/payments_';
        $fileName = date("d-M-Y-H-i-s") . rand(0, 90000) . '.pdf';

        file_put_contents('./pdf/invoices/payments_' . $fileName, $output);

        $response = array();
        $response['message'] = "";
        $response['success'] = true;
        $response['data']['filePath'] = $filePath . $fileName;

        $this->set_response($response, REST_Controller::HTTP_OK);

    }

    /*Generate Expenses PDF*/

    public function generate_expenses_pdf_post()
    {

        $this->load->helper(array('dompdf', 'file'));

        $userId = $this->post('userId', true);
        $dateFrom = $this->post('dateFrom', true);
        $dateTo = $this->post('dateTo', true);

        $data = array();

        $dates = $this->invoices_model->getDistinctDatesForExpensePDF($userId, $dateFrom, $dateTo);


        foreach ($dates as $key => $date) {
            $dates[$key]->items = $this->invoices_model->getFilteredInvoicesByDateForExpensePDF($userId, $dateFrom, $dateTo, $date);
        }
        $data = $dates;


        $html = $this->load->view('/pdf_templates/expense_invoice.php', array(
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ), true);

        $output = pdf_create($html);
        $filePath = '/pdf/invoices/expenses_';
        $fileName = date("d-M-Y-H-i-s") . rand(0, 90000) . '.pdf';

        file_put_contents('./pdf/invoices/expenses_' . $fileName, $output);

        $response = array();
        $response['message'] = "";
        $response['success'] = true;
        $response['data']['filePath'] = $filePath . $fileName;

        $this->set_response($response, REST_Controller::HTTP_OK);

    }

}