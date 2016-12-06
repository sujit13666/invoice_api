<?php

class Dashboard_model extends MY_Model
{
    //Table name for invoices
    public $table = 'invoices';

    public function __construct()
    {
        parent::__construct();
    }


    public function getAllSalesNumber($userId, $dateFrom, $dateTo){
        $this->db->select('COUNT(id) as count');
        $this->db->from('invoices i');

        $this->db->where('i.user_id',$userId);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $query = $this->db->get();
        return ($query ->result());

    }

    public function getAllSalesAmount($userId, $dateFrom, $dateTo){
        $this->db->select('SUM(total) as total');
        $this->db->from('invoices i');

        $this->db->where('i.user_id',$userId);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $query = $this->db->get();
        return ($query ->result());

    }

    public function getAllSalesPaidAmount($userId, $dateFrom, $dateTo){
        $this->db->select('SUM(total) as total');
        $this->db->from('invoices i');

        $this->db->where('i.user_id',$userId);
        $this->db->where('i.is_paid',1);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $query = $this->db->get();
        return ($query ->result());
    }

    public function getAllPaidInvoiceNumber($userId, $dateFrom, $dateTo){
        $this->db->select('COUNT(id) as count');
        $this->db->from('invoices i');

        $this->db->where('i.user_id',$userId);
        $this->db->where('i.is_paid',1);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $query = $this->db->get();
        return ($query ->result());
    }
    public function getAllUnpaidInvoiceNumber($userId, $dateFrom, $dateTo){
        $this->db->select('COUNT(id) as count');
        $this->db->from('invoices i');

        $this->db->where('i.user_id',$userId);
        $this->db->where('i.is_paid',0);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $query = $this->db->get();
        return ($query ->result());
    }
}