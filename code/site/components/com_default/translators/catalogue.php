<?php
/**
 * @version		$Id$
 * @package		Koowa_Translator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Translator Class
 *
 * @author		Ercan Ozkaya <ercan@timble.net>
 * @package		Koowa_Translator
 */
class ComDefaultTranslatorCatalogue extends KTranslatorCatalogue
{ 
    public function __get($key)
    {
        if(isset($this->_data[$key])) {
            $result = $this->_data[$key];
        } else {
            $result = $this->generateKey($key);
        }
    
        return $result;
    }

    public function generateKey($string)
    {
        $string = strtolower($string);
        
        if (strlen($string) > 40) {
            $key = $this->generateKey(substr($string, 0, 40));
            $key .= '_'.strtoupper(substr(md5($string), 0, 5));
        } else {
            $key = strip_tags($string);
            $key = preg_replace('#%([A-Za-z0-9_\-\.]+)%#', ' $1 ', $key);
            $key = preg_replace('#(%[^%|^\s|^\b]+)#', 'X', $key);
            $key = preg_replace('#&.*?;#', '', $key);
            $key = preg_replace('#[\s-]+#', '_', $key);
            $key = preg_replace('#[^A-Za-z0-9_]#', '', $key);
            $key = preg_replace('#_+#', '_', $key);
            $key = trim($key, '_');
            $key = trim(strtoupper($key));
        }

        $this->_data[$string] = $key;

        return $key;
    }
}