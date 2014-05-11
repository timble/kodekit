<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Translator
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Translator
 */
abstract class KTranslatorAbstract extends KObject implements KTranslatorInterface
{
    /**
     * Locale
     *
     * @var string
     */
    protected $_locale;

    /**
     * Catalogue to hold translation keys in JavaScript code
     *
     * @var KTranslatorCatalogueInterface
     */
    protected $_script_catalogue;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);
        
        $this->setLocale($config->locale);
        $this->setScriptCatalogue($this->createCatalogue($config->script_catalogue));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'locale' => 'en-GB',
            'script_catalogue' => 'script'
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * Translates a string and handles parameter replacements
     *
     * Parameters are wrapped in curly braces. So {foo} would be replaced with bar given that $parameters['foo'] = 'bar'
     * 
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
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
     * @return string String after replacing the parameters
     */
    public function replaceParameters($string, array $parameters = array())
    {
        $keys       = array_map(array($this, '_replaceKeys'), array_keys($parameters));
        $parameters = array_combine($keys, $parameters);

        return strtr($string, $parameters);
    }

    /**
     * Adds curly braces around keys to make strtr work in replaceParameters method
     *
     * @param string $key
     * @return string
     */
    protected function _replaceKeys($key)
    {
        return '{'.$key.'}';
    }

    /**
     * Translates a string based on the number parameter passed
     *
     * @param array   $strings Strings to choose from
     * @param integer $number The umber of items
     * @param array   $parameters An array of parameters
     * @throws InvalidArgumentException
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array())
    {
        if (count($strings) < 2) {
            throw new InvalidArgumentException('Choose method requires at least 2 strings to choose from');
        }
        
        $choice = KTranslatorInflector::getPluralPosition($number, $this->_locale);
        
        if ($choice > count($strings)-1) {
            $choice = count($strings)-1;
        }
        
        return $this->translate($strings[$choice], $parameters);
    }

    /**
     * Checks if a given string is translatable.
     *
     * @param  string $string The string to check.
     * @return bool True if it is, false otherwise.
     */
    public function isTranslatable($string)
    {
        return false;
    }

    /**
     * Sets the locale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;
    }

    /**
     * Gets the locale
     *
     * @return string|null
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Add a string and its translation to the script catalogue so that it gets sent to the browser later on
     *
     * @param  $string string The translation key
     * @return $this
     */
    public function addScriptTranslation($string)
    {
        $this->getScriptCatalogue()->offsetSet($string, $this->translate($string));

        return $this;
    }

    /**
     * Return the script catalogue
     *
     * @return KTranslatorCatalogueInterface
     */
    public function getScriptCatalogue()
    {
        return $this->_script_catalogue;
    }

    /**
     * Set the default catalogue
     *
     * @param KTranslatorCatalogueInterface $catalogue
     * @return $this
     */
    public function setScriptCatalogue(KTranslatorCatalogueInterface $catalogue)
    {
        $this->_script_catalogue = $catalogue;

        return $this;
    }

    /**
     * Creates and returns a catalogue from the passed identifier
     *
     * @param string|null $identifier Full identifier or just the name part
     * @return KTranslatorCatalogue
     */
    public function createCatalogue($identifier = null)
    {
        if (strpos($identifier, '.') === false)
        {
            $old = $this->getIdentifier()->toArray();

            if ($identifier)
            {
                $old['path'] = array('translator', 'catalogue');
                $old['name'] = $identifier;
            }
            else
            {
                $old['path'] = array('translator');
                $old['name'] = 'catalogue';
            }

            $identifier = $this->getIdentifier($old);
        }

        return $this->getObject($identifier);
    }
}
