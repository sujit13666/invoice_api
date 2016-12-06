<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        if (!isset($this->dashboard_model))
            $this->load->model('dashboard_model');

    }


    /* Get Dashboard data for app*/

    public function get_dashboard_data_post()
    {

        $userId = $this->post('userId', true);
        $year = $this->post('year', true);
        $dateFrom  = $year."-01-01";
        $dateTo  = $year."-12-31";


        $salesNumber=$this->dashboard_model->getAllSalesNumber($userId, $dateFrom, $dateTo);
        $salesAmount=$this->dashboard_model->getAllSalesAmount($userId, $dateFrom, $dateTo);
        $paidAmount = $this->dashboard_model->getAllSalesPaidAmount($userId, $dateFrom, $dateTo);
        $customerOwingAmount = $salesAmount[0]->total - $paidAmount[0]->total;
        $paidInvoiceNumber = $this->dashboard_model->getAllPaidInvoiceNumber($userId, $dateFrom, $dateTo);
        $unpaidInvoiceNumber = $this->dashboard_model->getAllUnpaidInvoiceNumber($userId, $dateFrom, $dateTo);

        $data=array(
            'salesNumber'=>$salesNumber[0]->count,
            'salesAmount'=>number_format($salesAmount[0]->total,2),
            'paidAmount'=>number_format($paidAmount[0]->total,2),
            'customerOwingAmount'=>number_format($customerOwingAmount,2),
            'paidInvoiceNumber'=>$paidInvoiceNumber[0]->count,
            'unpaidInvoiceNumber'=>$unpaidInvoiceNumber[0]->count

        );

        $response = array();
        $response['message'] = "";
        $response['success'] = true;
        $response['data'] =$data;

        $this->set_response($response, REST_Controller::HTTP_OK);

    }

}