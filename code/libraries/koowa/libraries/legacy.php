<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * PHP5.3 compatibility
 */
if(false === function_exists('lcfirst'))
{
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst( $str )
    {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}

if (!function_exists('array_replace_recursive'))
{
    function array_replace_recursive($array, $array1)
    {
        function recurse($array, $array1)
        {
            foreach ($array1 as $key => $value)
            {
                // create new key in $array, if it is empty or not an array
                if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                    $array[$key] = array();
                }

                // overwrite the value in the base array
                if (is_array($value)) {
                    $value = recurse($array[$key], $value);
                }

                $array[$key] = $value;
            }

            return $array;
        }

        // handle the arguments, merge one by one
        $args = func_get_args();
        $array = $args[0];
        if (!is_array($array)) {
            return $array;
        }

        for ($i = 1; $i < count($args); $i++)
        {
            if (is_array($args[$i])) {
                $array = recurse($array, $args[$i]);
            }
        }
        return $array;
    }
}

/**
 * APC 3.1.4 compatibility
 */
if(extension_loaded('apc') && !function_exists('apc_exists'))
{
    /**
     * Check if an APC key exists
     *
     * @param  mixed  $keys A string, or an array of strings, that contain keys.
     * @return boolean Returns TRUE if the key exists, otherwise FALSE
     */
    function apc_exists($keys)
    {
        $result = null;

        apc_fetch($keys,$result);

        return $result;
    }
}

/**
 * PHP5.4 compatibility
 *
 * @link http://nikic.github.io/2012/01/28/htmlspecialchars-improvements-in-PHP-5-4.html
 */
if (!defined('ENT_SUBSTITUTE'))
{
    if(!defined('ENT_IGNORE')) {
        define('ENT_SUBSTITUTE', 0);          //PHP 5.2 behavior
    } else {
        define('ENT_SUBSTITUTE', ENT_IGNORE); //PHP 5.3 behavior
    }
}
