<?php
/**
 * array_to_object
 * 
 * Converts an array to an object
 * 
 * @param array $array Any array
 * 
 * return object
 */
if (! function_exists('array_to_object')) {

    function array_to_object(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::array_to_object($value);
            }
        }
        return (object) $array;
    }
}