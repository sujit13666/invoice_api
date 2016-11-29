<?php

class Users_model extends MY_Model
{
	//Table name for users
	public $table = 'users';
	
    public function __construct()
    {        
        parent::__construct();
        
        if (! isset($this->ion_auth_model))
            $this->load->model('ion_auth_model');
    }

    /**
     * If it's a new record check for duplicate email addresses, before creating record
     * (non-PHPdoc)
     * @see MY_Model::save()
     */
    public function save($data = FALSE)
    {
        // If new record
        if (empty($data['rid']) && empty($data['id'])) {
            // Check if email already exists
            if ($this->ion_auth_model->identity_check($data['email'])) {
                $this->set_error('account_creation_duplicate_identity');
                return FALSE;
            }
        }
        
        // Hashing password
        if (! empty($data['password'])) {
            $salt = $this->ion_auth_model->store_salt ? $this->ion_auth_model->salt() : FALSE;
            $data['password'] = $this->ion_auth_model->hash_password($data['password'], $salt);
        }
        
        return parent::save($data);
    }

    /**
     * All this does additionall is merge firstname and lastname before returning results
     * 
     * (non-PHPdoc)
     * @see MY_Model::get_for_dropdown()
     */
    public function get_for_dropdown($name = FALSE, $select = FALSE, $where = FALSE, $translate = TRUE, $includeSelect = FALSE, $order_by = FALSE)
    {
        $users = parent::get_for_dropdown($name, $select, $where, $translate, $includeSelect, $order_by);
        
        // Will concatenate first_name and last_name if both fields were specified in $select.
        if (isset($users[0]) && isset($select['name']) && $select['name'] == "first_name" && isset($select['name2']) && $select['name2'] == "last_name") {
            for ($i = 0; $i < count($users); $i ++) {
                $users[$i]['name'] .= " " . $users[$i]['name2'];
            }
        }
        
        return $users;
    }

    /**
     * This will delete all associated data
     * 
     * (non-PHPdoc)
     * @see MY_Model::delete_where()
     */
    public function delete_where($where = FALSE)
    {
        if (! $where || ! isset($this->table) || ! $this->table || empty($where['id']))
            return FALSE;
            
            // Delete Estimates
        if (! isset($this->estimates_lines_model))
            $this->load->model('estimates_lines_model');
        if (! isset($this->estimates_model))
            $this->load->model('estimates_model');
        
        $this->estimates_lines_model->delete_where(array(
            'user_id' => $where['id']
        ));
        $this->estimates_model->delete_where(array(
            'user_id' => $where['id']
        ));
        
        // Delete Invoices
        if (! isset($this->invoices_lines_model))
            $this->load->model('invoices_lines_model');
        if (! isset($this->invoices_model))
            $this->load->model('invoices_model');
        
        $this->invoices_lines_model->delete_where(array(
            'user_id' => $where['id']
        ));
        $this->invoices_model->delete_where(array(
            'user_id' => $where['id']
        ));
        
        // Delete Clients
        if (! isset($this->clients_model))
            $this->load->model('clients_model');
        $this->clients_model->delete_where(array(
            'user_id' => $where['id']
        ));
        
        // Delete Items
        if (! isset($this->items_model))
            $this->load->model('items_model');
        $this->items_model->delete_where(array(
            'user_id' => $where['id']
        ));
        
        // Delete Settings
        if (! isset($this->settings_model))
            $this->load->model('settings_model');
        $this->settings_model->delete_where(array(
            'user_id' => $where['id']
        ));
        
        // Delete User Groups
        if (! isset($this->users_groups_model))
            $this->load->model('users_groups_model');
        $this->users_groups_model->delete_where(array(
            'user_id' => $where['id']
        ));
        
        return parent::delete_where($where);
    }
}