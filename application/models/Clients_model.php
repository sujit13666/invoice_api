<?php

class Clients_model extends MY_Model
{
    //Table name for clients
    public $table = 'clients';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * delete
     * 
     * Delete a client
     * 
     * @param string    $api_key   required    users api key
     * @param int       $user_id   required    users id
     * @param int       $id        required    client id to be deleted
     * 
     * @return int 1 or 0
     */
    public function delete($api_key = FALSE, $user_id = FALSE, $id = FALSE)
    {
        if (! $api_key || ! $user_id || ! $id)
            return FALSE;
        
        $data = $this->db->query("
		DELETE clients FROM clients
		LEFT JOIN `users` ON `users`.id = `clients`.user_id
		LEFT JOIN `keys` ON `keys`.id = `users`.api_key
		WHERE `clients`.id = ? AND `clients`.user_id = ? AND `keys`.key = ?;
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
        if (! $where || ! isset($this->table) || ! $this->table)
            return FALSE;
        
        if (! empty($where['id'])) {
            // Delete estimates
            if (! isset($this->estimates_lines_model))
                $this->load->model('estimates_lines_model');
            if (! isset($this->estimates_model))
                $this->load->model('estimates_model');
            
            $data = $this->db->query("
			DELETE estimates_lines FROM estimates_lines
			LEFT JOIN `estimates` ON `estimates`.id = `estimates_lines`.estimate_id
			WHERE `estimates`.client_id = ?;", array(
                $where['id']
            ));
            
            $this->estimates_model->delete_where(array(
                'client_id' => $where['id']
            ));
            
            // Delete invoices
            if (! isset($this->invoices_lines_model))
                $this->load->model('invoices_lines_model');
            if (! isset($this->invoices_model))
                $this->load->model('invoices_model');
            
            $data = $this->db->query("
			DELETE invoices_lines FROM invoices_lines
			LEFT JOIN `invoices` ON `invoices`.id = `invoices_lines`.invoice_id
			WHERE `invoices`.client_id = ?;", array(
                $where['id']
            ));
            
            $this->invoices_model->delete_where(array(
                'client_id' => $where['id']
            ));
        }
        
        return parent::delete_where($where);
    }
}