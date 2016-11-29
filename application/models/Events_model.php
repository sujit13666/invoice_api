<?php

class Events_model extends MY_Model
{
	//Table name for clients
    public $table = 'events';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * delete
     *
     * Delete an item
     *
     * @param string    $api_key   required    users api key
     * @param int       $user_id   required    users id
     * @param int       $id        required    item id to be deleted
     * 
     * @return int 1 or 0
     */
    public function delete($user_id = FALSE, $id = FALSE)
    {
        if (! $user_id || ! $id)
            return FALSE;
        
        $data = $this->db->query("
		DELETE items FROM items
		LEFT JOIN `users` ON `users`.id = `items`.user_id
		WHERE  `items`.id IN ($id) AND `items`.user_id = $user_id;
		");
        return $this->db->affected_rows(); 
        
    }
}