<?php

class Categories_model extends MY_Model
{
    //Table name for invoices
    protected $table = 'categories';

    public function __construct()
    {
        parent::__construct();
    }

    public function getCategories($userId){

        $this->db->select('id,category_name');
        $this->db->where('user_id', $userId);
        $this->db->or_where('category_type', "default");
        $query = $this->db->get('categories');
        return $query ->result();
    }



}