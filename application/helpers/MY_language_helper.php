<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 * If translation is not found it returns the same txt in upper case and with a starting/ending underscore
 *
 * @param string $line
 *            line
 * @param string $for
 *            value (id of the form element)
 * @param array $attributes
 *            HTML attributes
 * @return string
 */
if (! function_exists('lang')) {

    function lang($txt, $for = '', $attributes = array())
    {
        $line = get_instance()->lang->line($txt);
        
        if ($for !== '' && $line) {
            $line = '<label for="' . $for . '"' . _stringify_attributes($attributes) . '>' . $line . '</label>';
        }
        
        return $line ? $line : strtoupper('_' . $txt . '_');
    }
}
