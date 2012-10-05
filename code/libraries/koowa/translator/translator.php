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
class KTranslator extends KObject
{
    protected $_locale;
    
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->setLocale($config->locale);
    }
    
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'locale' => 'en-GB'
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * Translates a string and handles parameter replacements
     * 
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * 
     * @return string Translated strign
     */
    public function translate($string, array $parameters = array())
    {
        return strtr($string, $parameters);
    }
    
    /**
     * Translates a string based on the number parameter passed
     *
     * @param string  $strings Strings to choose from
     * @param integer $number The umber of items
     * @param array   $parameters An array of parameters
     * 
     * @throws InvalidArgumentException
     *
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array())
    {
        if (count($strings) < 2) {
            throw new InvalidArgumentException('Choose method requires at least 2 strings to choose from');
        }
        
        $choice = KTranslatorPluralizationrules::get($number, $this->_locale);
        
        if ($choice > count($strings)-1) {
            $choice = count($strings)-1;
        }
        
        return $this->translate($strings[$choice], $parameters);
    }
    
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        
        return $this;
    }
    
    public function getLocale()
    {
        return $this->_locale;
    }
}