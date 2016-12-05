<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base model
 *
 * All models from the models folder must extend this base model
 * It has CRUD method to work with data. All models/controller use these methods to interact with the database
 */
class MY_Model extends CI_Model
{

    protected $table;

    protected $errors;

    protected $error_start_delimiter;

    protected $error_end_delimiter;

    public $live_user;

    public function __construct()
    {
        $this->error_start_delimiter = $this->config->item('error_start_delimiter', 'ion_auth');
        $this->error_end_delimiter = $this->config->item('error_end_delimiter', 'ion_auth');
    }

    /**
     * get()
     * This method gets the result according to the provided parameters
     * 
     * @param array/string  $where   optional where condition. array('id'=>4)   
     * @param string        $select  optional the select part of the query "id, name"
     * @param string        $orderby optional order by part of the query            
     * @param int           $limit   optional limit the records         
     * @param int           $offset  optional used for pagination
     * 
     * @return array
     */
    public function get($where = FALSE, $select = FALSE, $orderby = false, $limit = FALSE, $offset = 0)
    {
        if (! isset($this->table) or ! $this->table)
            return FALSE;
            
            // Making sure all requests from non admins are adding a user_id
            // Call from rest api should ALWAYS have a user id
        if (empty($where['user_id']) && ! $this->ion_auth->is_admin() && isset($this->live_user) && $this->object_model->table != 'users') {
            $where[$this->object_model->table . '.user_id'] = $this->live_user->user_id;
        }
        
        if ($where)
            $this->db->where($where);
        
        if ($select)
            $this->db->select($select);
        
        if ($orderby)
            $this->db->order_by($orderby);
        
        $limit = ($limit) ? $limit : $this->config->item('rows_per_page');
        
        $result = $this->db->get($this->table, $limit, $offset)->result();
        
        return $result;
    }

    /**
     * load
     * 
     * Retrieves a specific record
     * 
     * @param string $rid   required    the RID of the record.
     * 
     * @return object
     */
    public function load($rid = FALSE)
    {
        if (! isset($this->table) or ! $this->table || ! $rid)
            return FALSE;
        
        $result = $this->get(array(
            'rid' => $rid
        ), false, false, 1);
        
        if ($result && isset($result[0]))
            return $result[0];
        
        return FALSE;
    }

    /**
     * delete_where
     * 
     * Creates and executes a delete a record where certain condition are met query
     *
     * @param array $where   required    array('id'=>4)
     * 
     * @return int 1 or 0
     */
    public function delete_where($where = FALSE)
    {
        if (! $where || ! isset($this->table) or ! $this->table)
            return FALSE;
        
        $this->db->where($where);
        
        if (isset($this->where) and $this->where)
            $this->db->where($this->where);
        
        $this->db->delete($this->table);
        
        $result = $this->db->affected_rows();
        
        return $result;
    }

    /**
     * save
     * Always call this method to save OR update a record. It will determine based on the id/rid whether it's a new record or an existing record
     *
     * @param array $data   required    array containing all the data
     * 
     * @return object
     */
    public function save($data = FALSE)
    {
        // If not table found then return FALSE
        if (! isset($this->table) or ! $this->table || ! $data)
            return FALSE;
            
            // If key exist it means the record exist and we need an update else insert the record
        if (! empty($data['rid']) || ! empty($data['id']))
            return $this->_update($data);
        else
            return $this->_save($data);
    }

    /**
     * _save
     * This is called by the save method. Don't call this method manually.
     *
     * @param array $data   required    array containing all the data
     * 
     * @return object
     */
    public function _save($data = FALSE)
    {
        if (! $data)
            return FALSE;
        
        $next_id = array();
        
        if (! isset($data['rid']) or (isset($data['rid']) and $data['rid'] == '')) {
            $data['rid'] = $this->set_rid();
        }
        
        if (! isset($data['created_on']) or (isset($data['created_on']) and $data['created_on'] == '')) {
            $data['created_on'] = time();
            $data['updated_on'] = $data['created_on'];
        }

        $result = $this->db->insert($this->table, $data);
        
        if ($result) {
            if (! isset($data['id']) || (isset($data['id']) && $data['id'] == '')) {
                $data['id'] = $this->db->insert_id();
            }
            
            return array_to_object($data);
        }
        
        return $result;
    }

    /**
     * _update
     * This is called by the save method. Don't call this method manually.
     *
     * @param array $data   required    array containing all the data
     * 
     * @return object
     */
    public function _update($data)
    {
        if (! $data)
            return FALSE;
        
        $data['updated_on'] = time();
        
        // If rid or key was set then use them for the where, otherwise return FALSE
        if (! empty($data['id']))
            $this->db->where('id', $data['id']);
        else 
            if (! empty($data['rid']))
                $this->db->where('rid', $data['rid']);
            else
                return FALSE;
        
        $result = $this->db->update($this->table, $data);
        
        if ($result) {
            return array_to_object($data);
        }
        
        return $result;
    }
    
    /**
     * count
     * This method is used to count the result of a query and return back a number
     *
     * @param array         $where   optional   where condition. array('id'=>4)   
     * @param string        $select  optional   the select part of the query "id, name"   
     * 
     * @return int
     */
    function count($where = FALSE, $select = FALSE)
    {
        if (! $select)
            $select = 'COUNT(id) as numrows';
        
        $result = $this->get($where, $select);
        
        if ($result and isset($result[0]->numrows))
            return $result[0]->numrows;
        else
            return 0;
    }

    /**
     * set_rid
     * Creates a 40 character long random string
     *
     * @param bool         $md5   optional   to use either md5 or sha for the encryption  
     * 
     * @return string
     */
    public function set_rid($md5 = FALSE)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $character_length = strlen($characters) - 1; // if we don't put -1 it will generate a notice error after few times running through
        
        $random_characters = '';
        
        for ($i = 0; $i < 10; $i ++) {
            $random_characters .= $characters[mt_rand(0, $character_length)];
        }
        
        if ($md5)
            return md5($random_characters . uniqid());
        else
            return sha1($random_characters . uniqid());
    }

    /**
     * set_error
     * 
     * set errors that happen during DB communication so that the controller can retrieve it and display it
     * @param string $error required error language code    
     *
     * @return string 
     */
    public function set_error($error)
    {
        $this->errors[] = $error;
        
        return $error;
    }

    /**
     * errors()
     * 
     * returns all error translated into the correct language
     * 
     * @return string
     */
    public function errors()
    {
        $_output = '';
        foreach ($this->errors as $error) {
            $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
            $_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
        }
        
        return $_output;
    }

    /**
     * clear_errors()
     *
     * clears all errors
     * 
     * @return boolean
     */
    public function clear_errors()
    {
        $this->errors = array();
        
        return TRUE;
    }

    /**
     * get_for_dropdown()
     * Returns formatted data for dropdowns.
     * 
     * @param
     *            $name
     * @param array/string $where            
     * @param array $select
     *            if array contains only 1 array("key"=>"product_type_name") it will treat it as key=>value and return $result[1] => phone
     *            if array contains several array("key"=>"value","tax_name"=>"name","tax_rate"=>"data-value") it will return $result[1] = array(value=>1,tax_name=>VAT20,data-value=>20)
     *            
     * @return array
     */
    public function get_for_dropdown($name = FALSE, $select = FALSE, $where = FALSE, $translate = TRUE, $includeSelect = FALSE, $orderby = FALSE)
    {
        $result = array();
        
        if (! $select or ! is_array($select))
            return FALSE;
        
        if ($where)
            $this->db->where($where);
        if ($orderby)
            $this->db->order_by($orderby);
        
        $tmp_result = $this->get();
        
        if ($tmp_result and isset($tmp_result[0]->id)) {
            if ($includeSelect)
                $result[- 1] = lang('please_select_' . $name);
            
            if (count($select) == 1) {
                $select_key = key($select);
                $select_value = $select[key($select)];
                
                foreach ($tmp_result as $val) {
                    $result[$val->$select_key] = $translate ? lang($val->$select_value) : $val->$select_value;
                }
            } elseif (count($select) > 1) {
                foreach ($tmp_result as $val) {
                    $data = array();
                    $uniquekey = "";
                    foreach ($select as $key => $value) {
                        if ($key == "value") {
                            $uniquekey = $value;
                            $data[$key] = $val->$value;
                        } elseif ($key == "name")
                            $data[$key] = $translate ? lang($val->$value) : $val->$value;
                        else {
                            $data[$key] = $val->$value;
                        }
                    }
                    $result[] = $data;
                }
            }
        }
        
        return $result;
    }
}
