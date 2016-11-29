<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Settings controller
 *
 * All settings action using the website is done here
 */
class Settings extends MY_Controller
{

    public function __construct()
    {
        $this->need_auth = TRUE;
        
        parent::__construct();
    }

    /**
     *
     * get_settings
     *
     * This will retrieve the settings information of the current user
     *
     * @param int $user_id required user id of the user you are requesting the settings information for
     */
    public function get_settings()
    {
        $data['result'] = $this->json_response($this->json_status['error']);
        
        if ($this->is_ajax()) {
            $user_id = $this->input->get('user_id', TRUE);
            
            if ($user_id) {
                $this->object_model->db->where(array(
                    'user_id' => $user_id
                ));
            }
            
            $settings = $this->object_model->get();
            
            if ($settings) {
                $content = json_decode($settings[0]->content);
                $result['currency_symbol'] = empty($content->currency_symbol) ? '$' : $content->currency_symbol;
                $result['invoice_number'] = empty($content->invoice_number) ? '0001' : sprintf('%04d', $content->invoice_number + 1);
                $result['estimate_number'] = empty($content->estimate_number) ? '0001' : sprintf('%04d', $content->estimate_number + 1);
            } else {
                $result['currency_symbol'] = '$';
                $result['invoice_number'] = sprintf('%04d', 1);
                $result['estimate_number'] = sprintf('%04d', 1);
            }
            
            $data['result'] = $this->json_response($this->json_status['success'], $result);
        }
        
        $this->template('/ajax', $data, TRUE);
    }
}
