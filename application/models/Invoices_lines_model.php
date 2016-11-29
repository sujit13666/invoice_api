<?php

class invoices_lines_model extends MY_Model
{
	//Table name for invoice lines
    public $table = 'invoices_lines';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * delete
     *
     * Delete an invoice line
     *
     * @param string    $api_key   required    users api key
     * @param int       $user_id   required    users id
     * @param int       $id        required    invoice line id to be deleted
     * 
     * @return int 1 or 0
     */
    public function delete($api_key = FALSE, $user_id = FALSE, $id = FALSE)
    {
        if (! $api_key || ! $user_id || ! $id)
            return FALSE;
            
            // Delete all invoice lines, before deleting invoice
        $data = $this->db->query("
		DELETE invoices_lines FROM invoices_lines
		LEFT JOIN `users` ON `users`.id = `invoices_lines`.user_id
		LEFT JOIN `keys` ON `keys`.id = `users`.api_key
		WHERE `invoices_lines`.id = ? AND `invoices`.user_id = ? AND `keys`.key = ?;
		", array(
            $id,
            $user_id,
            $api_key
        ));
        
        return $data;
    }
}