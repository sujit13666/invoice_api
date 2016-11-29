<?php

class Estimates_model extends MY_Model
{
	//Table name for clients
    public $table = 'estimates';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * delete
     *
     * Delete an estimate
     *
     * @param string    $api_key   required    users api key
     * @param int       $user_id   required    users id
     * @param int       $id        required    estimate id to be deleted
     * 
     * @return int 1 or 0
     */
    public function delete($api_key = FALSE, $user_id = FALSE, $id = FALSE)
    {
        if (! $api_key || ! $user_id || ! $id)
            return FALSE;
            
            // Delete all Estimate lines, before deleting Estimate
        $data = $this->db->query("
		DELETE Estimates_lines FROM Estimates_lines
		LEFT JOIN `users` ON `users`.id = `Estimates_lines`.user_id
		LEFT JOIN `keys` ON `keys`.id = `users`.api_key
		WHERE `Estimates_lines`.Estimate_id = ? AND `Estimates_lines`.user_id = ? AND `keys`.key = ?;
		", array(
            $id,
            $user_id,
            $api_key
        ));
        
        // Delete Estimate
        $data = $this->db->query("
		DELETE Estimates FROM Estimates
		LEFT JOIN `users` ON `users`.id = `Estimates`.user_id
		LEFT JOIN `keys` ON `keys`.id = `users`.api_key
		WHERE `Estimates`.id = ? AND `Estimates`.user_id = ? AND `keys`.key = ?;
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
            if (! isset($this->estimates_lines_model))
                $this->load->model('estimates_lines_model');
            $this->estimates_lines_model->delete_where(array(
                'estimate_id' => $where['id']
            ));
        }
        
        return parent::delete_where($where);
    }
}