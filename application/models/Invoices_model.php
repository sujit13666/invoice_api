<?php

class Invoices_model extends MY_Model
{
    //Table name for invoices
    public $table = 'invoices';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * delete
     *
     * Delete an invoice
     *
     * @param string $api_key required    users api key
     * @param int $user_id required    users id
     * @param int $id required    invoice id to be deleted
     *
     * @return int 1 or 0
     */
    public function delete($api_key = FALSE, $user_id = FALSE, $id = FALSE)
    {
        if (!$api_key || !$user_id || !$id)
            return FALSE;

        // Delete all invoice lines, before deleting invoice
        $data = $this->db->query("
		DELETE invoices_lines FROM invoices_lines
		LEFT JOIN `users` ON `users`.id = `invoices_lines`.user_id
		LEFT JOIN `keys` ON `keys`.id = `users`.api_key
		WHERE `invoices_lines`.invoice_id = ? AND `invoices_lines`.user_id = ? AND `keys`.key = ?;
		", array(
            $id,
            $user_id,
            $api_key
        ));

        // Delete invoice
        $data = $this->db->query("
		DELETE invoices FROM invoices
		LEFT JOIN `users` ON `users`.id = `invoices`.user_id
		LEFT JOIN `keys` ON `keys`.id = `users`.api_key
		WHERE `invoices`.id = ? AND `invoices`.user_id = ? AND `keys`.key = ?;
		", array(
            $id,
            $user_id,
            $api_key
        ));

        return $data;
    }

    /**
     * (non-PHPdoc)
     * @see MY_Model::delete_where()
     */
    public function delete_where($where = FALSE)
    {
        if (!$where || !isset($this->table) || !$this->table)
            return FALSE;

        if (!empty($where['id'])) {
            if (!isset($this->invoices_lines_model))
                $this->load->model('invoices_lines_model');
            $this->invoices_lines_model->delete_where(array(
                'invoice_id' => $where['id']
            ));
        }

        return parent::delete_where($where);
    }


    /**
     * @param bool|FALSE $user_id
     * @return mixed
     */
    public function get_max_invoice($user_id = FALSE)
    {
        // Delete all invoice lines, before deleting invoice
       $data = $this->db->query("SELECT MAX(invoice_number) AS invoice_number FROM invoices WHERE invoices.user_id = $user_id");

        return $data->result();
    }

    public function get_max_invoice_id()
    {

        // Delete all invoice lines, before deleting invoice
        $data = $this->db->query("SELECT MAX(id) AS id FROM invoices");

        return $data->result();
    }

    public function getDistinctDatesForPDF($userId, $dateFrom, $dateTo){

        $this->db->distinct();
        $this->db->select('i.invoice_date');
        $this->db->from('invoices i');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date <=', $dateTo);

        $query = $this->db->get();
       return ($query ->result());

    }
    public function getFilteredInvoicesByDateForPDF($userId, $dateFrom, $dateTo, $date){

        $this->db->select('i.id as invoice_id, c.id as client_id, c.business_name, i.user_id,i.subtotal, i.vat, i.total,i.paid,i.comment,i.is_paid,i.invoice_date,i.attached_image');
        $this->db->from('invoices i');
        $this->db->join('clients c', 'c.id=i.client_id', 'left');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $this->db->where('i.invoice_date', $date->invoice_date);

        $query = $this->db->get();
        return ($query ->result());


    }


    public function getDistinctCustomersForPDF($userId, $dateFrom, $dateTo){

        $this->db->distinct();
        $this->db->select('c.business_name , c.id');
        $this->db->from('invoices i');
        $this->db->join('clients c', 'c.id=i.client_id', 'left');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date <=', $dateTo);

        $query = $this->db->get();
        return ($query ->result());

    }
    public function getFilteredInvoicesByCustomerForPDF($userId, $dateFrom, $dateTo, $customer){

        $this->db->select('i.id as invoice_id, c.id as client_id, c.business_name, i.user_id,i.subtotal, i.vat, i.total,i.paid,i.comment,i.is_paid,i.invoice_date,i.attached_image');
        $this->db->from('invoices i');
        $this->db->join('clients c', 'c.id=i.client_id', 'left');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $this->db->where('c.id', $customer->id);

        $query = $this->db->get();
        return ($query ->result());


    }




    public function getDistinctDatesForPaymentPDF($userId, $dateFrom, $dateTo){

        $this->db->distinct();
        $this->db->select('i.invoice_date');
        $this->db->from('invoices i');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.is_paid',1);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date <=', $dateTo);

        $query = $this->db->get();
        return ($query ->result());

    }
    public function getFilteredInvoicesByDateForPaymentPDF($userId, $dateFrom, $dateTo, $date){

        $this->db->select('i.id as invoice_id, c.id as client_id, c.business_name, i.user_id,i.subtotal, i.vat, i.total,i.paid,i.comment,i.is_paid,i.invoice_date,i.attached_image');
        $this->db->from('invoices i');
        $this->db->join('clients c', 'c.id=i.client_id', 'left');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.is_paid',1);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $this->db->where('i.invoice_date', $date->invoice_date);

        $query = $this->db->get();
        return ($query ->result());


    }


    public function getDistinctCustomersForPaymentPDF($userId, $dateFrom, $dateTo){

        $this->db->distinct();
        $this->db->select('c.business_name , c.id');
        $this->db->from('invoices i');
        $this->db->join('clients c', 'c.id=i.client_id', 'left');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.is_paid',1);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date <=', $dateTo);

        $query = $this->db->get();
        return ($query ->result());

    }
    public function getFilteredInvoicesByCustomerForPaymentPDF($userId, $dateFrom, $dateTo, $customer){

        $this->db->select('i.id as invoice_id, c.id as client_id, c.business_name, i.user_id,i.subtotal, i.vat, i.total,i.paid,i.comment,i.is_paid,i.invoice_date,i.attached_image');
        $this->db->from('invoices i');
        $this->db->join('clients c', 'c.id=i.client_id', 'left');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.is_paid',1);
        $this->db->where('i.invoice_date >=', $dateFrom);
        $this->db->where('i.invoice_date  <=', $dateTo);
        $this->db->where('c.id', $customer->id);

        $query = $this->db->get();
        return ($query ->result());


    }


    public function getDistinctDatesForExpensePDF($userId, $dateFrom, $dateTo){

        $this->db->distinct();
        $this->db->select('i.item_date');
        $this->db->from('items i');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.item_date >=', $dateFrom);
        $this->db->where('i.item_date <=', $dateTo);

        $query = $this->db->get();
        return ($query ->result());

    }
    public function getFilteredInvoicesByDateForExpensePDF($userId, $dateFrom, $dateTo, $date){

        $this->db->select('i.id as item_id, i.name, i.cost, i.item_date');
        $this->db->from('items i');
        $this->db->where('i.user_id',$userId);
        $this->db->where('i.item_date >=', $dateFrom);
        $this->db->where('i.item_date  <=', $dateTo);
        $this->db->where('i.item_date', $date->item_date);

        $query = $this->db->get();
        return ($query ->result());


    }

}