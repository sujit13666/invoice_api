<?php
/**
 * my_form_dropdown
 * 
 * Creates a HTML select dropdown
 * 
 * @param	string	$name		name of the form
 * @param 	array 	$options	list of all values Array ( [0] => Array ( [value] => 1 [name] => Peter ) [1] => Array ( [value] => 2 [name] => James ) )
 * @param	array	$selected	list of selected values Array (1)
 * @param 	string	$extra		additional html properties for the form
 * 
 * @return string
 */
if (! function_exists('my_form_dropdown')) {

    function my_form_dropdown($name = '', $options = array(), $selected = array(), $extra = '')
    {
        if ((isset($options[0]) and is_array($options[0])) or (isset($options[1]) and is_array($options[1]))) {
            if (! is_array($selected)) {
                $selected = array(
                    $selected
                );
            }
            
            // If no selected state was submitted we will attempt to set it automatically
            if (count($selected) === 0) {
                // If the form name appears in the $_POST array we have a winner!
                if (isset($_POST[$name])) {
                    $selected = array(
                        $_POST[$name]
                    );
                }
            }
            
            if ($extra != '')
                $extra = ' ' . $extra;
            
            $multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';
            
            $form = '<select name="' . $name . '"' . $extra . $multiple . ">\n";
            
            foreach ($options as $key => $val) {
                if (is_array($val) && ! empty($val)) {
                    $optionKey = "";
                    $optionValue = "";
                    $optionOptions = "";
                    foreach ($val as $optgroup_key => $optgroup_val) {
                        if ($optgroup_key == 'value') {
                            if (in_array($optgroup_val, $selected))
                                $optionOptions .= ' selected="selected"';
                            $optionOptions .= ' value="' . $optgroup_val . '"';
                        } elseif ($optgroup_key == 'name')
                            $optionValue = $optgroup_val;
                        else
                            $optionOptions .= ' ' . $optgroup_key . '=' . $optgroup_val;
                        
                        // $sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
                    }
                    
                    $form .= '<option ' . $optionOptions . '>' . $optionValue . "</option>\n";
                } else {
                    $key = (string) $key;
                    $sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
                    
                    $form .= '<option value="' . $key . '"' . $sel . '>' . $val . "</option>\n";
                }
            }
            
            $form .= '</select>';
            
            return $form;
        } else {
            return form_dropdown($name, $options, $selected, $extra);
        }
    }
}