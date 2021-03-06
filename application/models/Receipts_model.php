<?php

class Receipts_model extends MY_Model
{
    //Table name for invoices
    protected $table = 'receipts';

    public function __construct()
    {
        parent::__construct();
    }

    public function removeReceipts($userId,$receipts){

        $this->db->where('user_id', $userId);
        $this->db->where_in('id', $receipts);
        $this->db->delete('receipts');

        if($this->db->affected_rows()){
            return true;
        }else{
            return false;
        }
    }

    public function getReceipts($userId){

        $this->db->select('r.id as receipt_id, c.id as category_id, c.category_name, v.id as vendor_id, v.vendor_name, r.user_id,r.tax, r.tip, r.total,r.receipt_description,r.receipt_date,r.receipt_image');
        $this->db->from('receipts r');
        $this->db->join('categories c', 'c.id=r.category_id', 'left');
        $this->db->join('vendors v', 'v.id=r.vendor_id', 'left');
        $this->db->where('r.user_id',$userId);
        $query = $this->db->get();
        return($query ->result());

    }

    public function getFilteredReceiptsByCategoryForPDF($userId,$dateFrom ,$dateTo,$category){

        $this->db->select('r.id as receipt_id, c.id as category_id, c.category_name, v.id as vendor_id, v.vendor_name, r.user_id,r.tax, r.tip, r.total,r.receipt_description,r.receipt_date,r.receipt_image');
        $this->db->from('receipts r');
        $this->db->join('categories c', 'c.id=r.category_id', 'left');
        $this->db->join('vendors v', 'v.id=r.vendor_id', 'left');
        $this->db->where('r.user_id',$userId);
        $this->db->where('r.receipt_date >=', $dateFrom);
        $this->db->where('r.receipt_date <=', $dateTo);
        $this->db->where('r.category_id', $category->category_id);

        $query = $this->db->get();
        return ($query ->result());

    }

    public function getDistinctCategoriesForPDF($userId,$dateFrom ,$dateTo){
        $this->db->distinct();
        $this->db->select('c.id as category_id,c.category_name');
        $this->db->from('receipts r');
        $this->db->join('categories c', 'c.id=r.category_id', 'left');
        $this->db->join('vendors v', 'v.id=r.vendor_id', 'left');
        $this->db->where('r.user_id',$userId);
        $this->db->where('r.receipt_date >=', $dateFrom);
        $this->db->where('r.receipt_date <=', $dateTo);

        $query = $this->db->get();
        return $query ->result();
    }

    public function getFilteredReceiptsByVendorForPDF($userId,$dateFrom ,$dateTo,$vendor){

        $this->db->select('r.id as receipt_id, c.id as category_id, c.category_name, v.id as vendor_id, v.vendor_name, r.user_id,r.tax, r.tip, r.total,r.receipt_description,r.receipt_date,r.receipt_image');
        $this->db->from('receipts r');
        $this->db->join('categories c', 'c.id=r.category_id', 'left');
        $this->db->join('vendors v', 'v.id=r.vendor_id', 'left');
        $this->db->where('r.user_id',$userId);
        $this->db->where('r.receipt_date >=', $dateFrom);
        $this->db->where('r.receipt_date <=', $dateTo);
        $this->db->where('r.vendor_id', $vendor->vendor_id);

        $query = $this->db->get();
        return ($query ->result());

    }

    public function getDistinctVendorsForPDF($userId,$dateFrom ,$dateTo){
        $this->db->distinct();
        $this->db->select('v.id as vendor_id,v.vendor_name');
        $this->db->from('receipts r');
        $this->db->join('categories c', 'c.id=r.category_id', 'left');
        $this->db->join('vendors v', 'v.id=r.vendor_id', 'left');
        $this->db->where('r.user_id',$userId);
        $this->db->where('r.receipt_date >=', $dateFrom);
        $this->db->where('r.receipt_date <=', $dateTo);

        $query = $this->db->get();
        return $query ->result();
    }

    public function getFilteredReceiptsByDateForPDF($userId,$dateFrom ,$dateTo,$date){

        $this->db->select('r.id as receipt_id, c.id as category_id, c.category_name, v.id as vendor_id, v.vendor_name, r.user_id,r.tax, r.tip, r.total,r.receipt_description,r.receipt_date,r.receipt_image');
        $this->db->from('receipts r');
        $this->db->join('categories c', 'c.id=r.category_id', 'left');
        $this->db->join('vendors v', 'v.id=r.vendor_id', 'left');
        $this->db->where('r.user_id',$userId);
        $this->db->where('r.receipt_date >=', $dateFrom);
        $this->db->where('r.receipt_date <=', $dateTo);
        $this->db->where('r.receipt_date', $date->receipt_date);

        $query = $this->db->get();
        return ($query ->result());


    }

    public function getDistinctDatesForPDF($userId,$dateFrom ,$dateTo){
        $this->db->distinct();
        $this->db->select('r.receipt_date');
        $this->db->from('receipts r');
        $this->db->join('categories c', 'c.id=r.category_id', 'left');
        $this->db->join('vendors v', 'v.id=r.vendor_id', 'left');
        $this->db->where('r.user_id',$userId);
        $this->db->where('r.receipt_date >=', $dateFrom);
        $this->db->where('r.receipt_date <=', $dateTo);

        $query = $this->db->get();
        return $query ->result();
    }
}