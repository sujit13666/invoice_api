<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RESTful Invoices
 *
 * Retrieve, delete, create and update invoices through the RESTful api *
 * http://yourdomain.com/index.php/api/invoices
 */
class Receipts extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        if (!isset($this->receipts_model))
            $this->load->model('receipts_model');
        if (!isset($this->categories_model))
            $this->load->model('categories_model');
        if (!isset($this->vendors_model))
            $this->load->model('vendors_model');


    }

    /*Add Receipt*/
    public function create_receipt_post()
    {
        $categoryId = $this->post('categoryId', true);
        $categoryName = $this->post('categoryName', true);
        $vendorId = $this->post('vendorId', true);
        $vendorName = $this->post('vendorName', true);
        $userId = $this->post('userId', true);
        $tax = $this->post('tax', true);
        $tip = $this->post('tip', true);
        $total = $this->post('total', true);
        $description = $this->post('description', true);
        $date = $this->post('date', true);


        //Image Uplaod
        $config['upload_path'] = './images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $ext = pathinfo($_FILES["receiptFile"]['name'], PATHINFO_EXTENSION);
        $new_name = $userId . '_' . time() . str_shuffle('12345abcde') . '.' . $ext;
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
        $this->upload->do_upload('receiptFile');


        $attachedImage = $config['file_name'];

        try {
            if ($categoryId == 0) {
                //Create new category and get the categoryId
                $category = $this->insert_category($categoryName, $userId);
                $categoryId = $category->id;
            }

            if ($vendorId == 0) {
                //Create new vendor and get the vendorId
                $vendor = $this->insert_vendor($vendorName, $userId);
                $vendorId = $vendor->id;
            }

            if ($this->insert_receipt($userId, $vendorId, $categoryId, $tax, $tip, $total, $description, $date, $attachedImage)) {
                $response['message'] = lang("record_creation_successful");
                $response['success'] = true;

                $this->set_response($response, REST_Controller::HTTP_CREATED);
            } else {
                $response['message'] = lang("record_creation_successful");
                $response['success'] = true;
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }

        } catch (Exception $e) {
            $response['message'] = lang("data_error");
            $response['success'] = false;
            $response['data'] = $e->getMessage();
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        }


    }

    /*Get Categories*/
    public function get_categories_post()
    {
        $userId = $this->post('userId', true);
        $categories = $this->categories_model->getCategories($userId);

        $response['message'] = "";
        $response['success'] = true;
        $response['data']['categories'] = $categories;
        $this->set_response($response, REST_Controller::HTTP_OK);

    }

    /*Get Vendors*/
    public function get_vendors_post()
    {
        $userId = $this->post('userId', true);
        $vendors = $this->vendors_model->getVendors($userId);

        $response['message'] = "";
        $response['success'] = true;
        $response['data']['vendors'] = $vendors;
        $this->set_response($response, REST_Controller::HTTP_OK);

    }


    /*Remove Receipts*/
    public function remove_receipts_post()
    {
        $userId = $this->post('userId', true);
        $receipts = $this->post('receipts', true);

        if ($this->receipts_model->removeReceipts($userId, $receipts)) {
            $response['message'] = "record_successfully_removed";
            $response['success'] = true;
            $this->set_response($response, REST_Controller::HTTP_OK);

        } else {
            $response['message'] = "record_remove_unsuccessful";
            $response['success'] = false;
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);

        }

    }

    /*Get  Receipts*/
    public function get_receipts_post()
    {
        $userId = $this->post('userId', true);

        $receipts = $this->receipts_model->getReceipts($userId);

        $response['message'] = "";
        $response['success'] = true;
        $response['data']['receipts'] = $receipts;
        $this->set_response($response, REST_Controller::HTTP_OK);


    }


    /*Generate Receipts PDF*/
    public function generate_receipts_pdf_post()
    {

        $this->load->helper(array('dompdf', 'file'));

        $userId = $this->post('userId', true);
        $dateFrom = $this->post('dateFrom', true);
        $dateTo = $this->post('dateTo', true);
        $sortType = $this->post('sortType', true);

        $data = array();

        if ($sortType == "category") {
            $categories = $this->receipts_model->getDistinctCategoriesForPDF($userId, $dateFrom, $dateTo);

            foreach ($categories as $key => $category) {

                $categories[$key]->receipts = $this->receipts_model->getFilteredReceiptsByCategoryForPDF($userId, $dateFrom, $dateTo, $category);

            }
            $data = $categories;

        } elseif ($sortType == "vendor") {

            $vendors = $this->receipts_model->getDistinctVendorsForPDF($userId, $dateFrom, $dateTo);

            foreach ($vendors as $key => $vendor) {

                $vendors[$key]->receipts = $this->receipts_model->getFilteredReceiptsByVendorForPDF($userId, $dateFrom, $dateTo, $vendor);

            }
            $data = $vendors;

        } elseif ($sortType == "date") {
            $dates = $this->receipts_model->getDistinctDatesForPDF($userId, $dateFrom, $dateTo);

            foreach ($dates as $key => $date) {

                $dates[$key]->receipts = $this->receipts_model->getFilteredReceiptsByDateForPDF($userId, $dateFrom, $dateTo, $date);

            }
            $data = $dates;
        } else {

        }


        $html = $this->load->view('/pdf_templates/receipt.php', array(
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortType' => $sortType
        ), true);

        $output = pdf_create($html);
        $filePath = '/pdf/receipts/receipt_';
        $fileName = date("d-M-Y-H-i-s") . rand(0, 90000) . '.pdf';

        file_put_contents('./pdf/receipts/receipt_' . $fileName, $output);

        $response = array();
        $response['message'] = "";
        $response['success'] = true;
        $response['data']['filePath'] = $filePath . $fileName;

        $this->set_response($response, REST_Controller::HTTP_OK);

    }


    /*Utility Functions*/
    private function insert_category($categoryName, $userId)
    {

        $data = array(
            'category_name' => $categoryName,
            'user_id' => $userId,
            'category_type' => "user_made",
        );
        return $this->categories_model->save($data);


    }

    private function insert_vendor($vendorName, $userId)
    {

        $data = array(
            'vendor_name' => $vendorName,
            'user_id' => $userId
        );
        return $this->vendors_model->save($data);


    }

    private function insert_receipt($userId, $vendorId, $categoryId, $tax, $tip, $total, $description, $date, $attachedImage)
    {

        $data = array(
            'user_id' => $userId,
            'vendor_id' => $vendorId,
            'category_id' => $categoryId,
            'tax' => $tax,
            'tip' => $tip,
            'total' => $total,
            'receipt_description' => $description,
            'receipt_date' => $date,
            'receipt_image' => $attachedImage,
        );

        return $this->receipts_model->save($data);


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
                'invoices.is_invoice' => 1
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
                'invoices.is_invoice' => 0
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
            ), $select);

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

}