<?php

class Estimates_lines_model extends MY_Model
{
	//Table name for estimate lines
    public $table = 'estimates_lines';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * delete
     *
     * Delete estimate lines
     *
     * @param string    $api_key   required    users api key
     * @param int       $user_id   required    users id
     * @param int       $id        required    estimate line id to be deleted
     * 
     * @return int 1 or 0
     */
    public function delete($api_key = FALSE, $user_id = FALSE, $id = FALSE)
    {
        if (! $api_key || ! $user_id || ! $id)
            return FALSE;
            
            // Delete all estimate lines, before deleting estimate
        $data = $this->db->query("
		DELETE estimates_lines FROM estimates_lines
		LEFT JOIN `users` ON `users`.id = `estimates_lines`.user_id
		LEFT JOIN `keys` ON `keys`.id = `users`.api_key
		WHERE `estimates_lines`.estimate_id = ? AND `estimates`.user_id = ? AND `keys`.key = ?;
		", array(
            $id,
            $user_id,
            $api_key
        ));
        
        return $data;
    }
}