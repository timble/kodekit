<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator Catalogue
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTranslatorCatalogue extends KTranslatorCatalogue
{
    /**
     * Caches and returns the generated key
     *
     * @param string $key
     * @return string Generated key
     */
    public function get($key)
    {
        if (!isset($this->_data[$key])) {
            $this->_data[$key] = $this->generateKey($key);
        }
        
        return $this->_data[$key];
    }

    /**
     * Generates a translation key that is safe for INI format
     *
     * @param  string $string
     * @return string
     */
    public function generateKey($string)
    {
        $string = strtolower($string);
        
        if (strlen($string) > 40)
        {
            $key = $this->generateKey(substr($string, 0, 40));
            $key .= '_'.strtoupper(substr(md5($string), 0, 5));
        }
        else
        {
            $key = strip_tags($string);
            $key = preg_replace('#\s+#m', ' ', $key);
            $key = preg_replace('#\{([A-Za-z0-9_\-\.]+)\}#', '$1', $key);
            $key = preg_replace('#(%[^%|^\s|^\b]+)#', 'X', $key);
            $key = preg_replace('#&.*?;#', '', $key);
            $key = preg_replace('#[\s-]+#', '_', $key);
            $key = preg_replace('#[^A-Za-z0-9_]#', '', $key);
            $key = preg_replace('#_+#', '_', $key);
            $key = trim($key, '_');
            $key = trim(strtoupper($key));
        }

        return $key;
    }
}
