<?php

class Settings_model extends MY_Model
{
	//Table name for clients
    public $table = 'settings';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * update
     * 
     * Update settings
     * @param array $data required  must contain data['user_id'] of the user and data['setting_name'] and data['setting_value']
     * 
     * @return boolean
     */
    public function update($data = FALSE)
    {
        if (! $data || $data && ! isset($data['user_id']))
            return FALSE;
        
        $settings = $this->get(array(
            'user_id' => $data['user_id']
        ), null, null, 1);
        
        if ($settings) {
            $array_settings = (array) $settings[0];
            
            $content = (array) json_decode($array_settings['content']);
            
            $content[$data['setting_name']] = $data['setting_value'];
            
            $array_settings['content'] = json_encode($content);
            
            return $this->save($array_settings);
        }
        
        return FALSE;
    }
}