<?php

class Vendors_model extends MY_Model
{
    //Table name for invoices
    protected $table = 'vendors';

    public function __construct()
    {
        parent::__construct();
    }

    public function getVendors($userId){

        $this->db->select('id,vendor_name');
        $this->db->where('user_id', $userId);
        $query = $this->db->get('vendors');
        return $query ->result();
    }

}