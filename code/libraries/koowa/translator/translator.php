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
     * Parameters are wrapped in curly braces.
     * So {foo} would be replaced with bar given that $parameters['foo'] = 'bar'
     * 
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * 
     * @return string Translated strign
     */
    public function translate($string, array $parameters = array())
    {
        if (count($parameters)) {
            $string = $this->replaceParameters($string, $parameters);
        }

        return $string;
    }

    /**
     * Handles parameter replacements
     *
     * @param string $string String
     * @param array  $parameters An array of parameters
     *
     * @return string String after replacing the parameters
     */
    public function replaceParameters($string, array $parameters = array())
    {
        $keys = array_map(array($this, '_replaceKeys'), array_keys($parameters));

        $parameters = array_combine($keys, $parameters);

        return strtr($string, $parameters);
    }

    /**
     * Adds curly braces around keys to make strtr work in replaceParameters method
     *
     * @param $key
     * @return string
     */
    protected function _replaceKeys($key)
    {
        return '{'.$key.'}';
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

    /**
     * Checks if the translator can translate a string
     *
     * @param $string String to check
     * @return bool
     */
    public function isTranslatable($string)
    {
        return false;
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