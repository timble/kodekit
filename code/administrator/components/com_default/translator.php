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
class ComDefaultTranslator extends KTranslator
{
    /**
     * A reference to Joomla translator
     * @var object
     */
    protected $_translator;
    
    /**
     * A prefix attached to every generated key
     * @var string
     */
    protected $_prefix;
    
    /**
     * Catalogue to map common Joomla keys
     * @var KTranslatorCatalogueInterface
     */
    protected $_alias_catalogue;
    
    /**
     * Default catalogue that generates the keys
     * @var KTranslatorCatalogueInterface
     */
    protected $_catalogue;
    
    /**
     * Maps identifier types to words
     * @var array
     */
    protected static $_type_map = array(
        'com' => 'component',
        'mod' => 'module',
        'plg' => 'plugin'
    );
    
    /**
     * An array of signatures from loaded language files 
     * @var array
     */
    protected static $_loaded_files = array();
    
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->setTranslator($config->translator);
        $this->setPrefix($config->prefix);
        
        $this->setDefaultCatalogue($this->createCatalogue($config->catalogue));
        $this->setAliasCatalogue($this->createCatalogue($config->alias_catalogue));
    }
    
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'locale'     => JFactory::getConfig()->get('language'),
            'translator' => JFactory::getLanguage(),
            'prefix'     => 'JT_',
            'catalogue'  => null,
            'alias_catalogue' => 'aliases'
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
        $result = strtolower($string);

        if (isset($this->_alias_catalogue[$result])) {
            $result = $this->_translator->_($this->_alias_catalogue[$result]);
        } else {
            $key = $this->getKey($string);
            $result = $this->_translator->_($this->_translator->hasKey($key) ? $key : $string);
        }
    
        return parent::translate($result, $parameters);
    }
    
    /**
     * Gets a key from the catalogue and prefixes it
     * 
     * @param string $string Language key
     * 
     * @return string Translated string
     */
    public function getKey($string)
    {
        $key = $this->_catalogue[$string];
        
        if ($this->_prefix) {
            $key = $this->_prefix.$key;
        }
        
        return $key;
    }
    
    /**
     * Load the extension language files.
     * 
     * First looking at extension folder and then the global language folder
     * @param string $extension Extension. Leave blank to get from the identifier.
     * @param string $base Base application. Leave blank to get from Joomla.
     * 
     * @throws KTranslatorException
     * 
     * @return boolean True if loading succeeds
     */
    public function loadLanguageFiles($extension = null, $base = null)
    {
        if ($extension === null) {
            $identifier = $this->getIdentifier();
            $type       = $identifier->type;
            $extension  = $type.'_'.$identifier->package;
        } else {
            $type = substr($extension, 0, 3);
        }
        
        if ($base === null) {
            $base = JPATH_BASE;
        }
        
        if (isset(self::$_type_map[$type])) {
            $type = self::$_type_map[$type];
        } else {
            throw new KTranslatorException(sprintf('Invalid extension type: %s', $type));
        }
        
        $ext_base = sprintf('%s/%ss/%s', $base, $type, $extension);
        
        $results = array();
        $results[] = $this->_loadLanguageFile($extension, $this->getFallbackLocale(), array($ext_base, $base));

        if ($this->getLocale() !== $this->getFallbackLocale()) {
            $results[] = $this->_loadLanguageFile($extension, $this->getLocale(), array($ext_base, $base));
        }
        
        return in_array(true, $results);
    }
    
    /**
     * Loads a Joomla language file
     * 
     * @param string $component Component name
     * @param string $locale Locale name
     * @param array  $base Base path list
     */
    protected function _loadLanguageFile($extension, $locale, array $base)
    {
        $result = false;
        
        foreach ($base as $path) {
            $signature = md5($extension.$path.$locale);

            $result = in_array($signature, self::$_loaded_files) 
                          || $this->_translator->load($extension, $path, $locale);
            
            if ($result) {
                if (!in_array($signature, self::$_loaded_files)) {
                    self::$_loaded_files[] = $signature;
                }
                
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * Creates and returns a catalogue from the passed identifier 
     * 
     * @param string|null $identifier Full identifier or just the name part
     * 
     * @return KTranslatorCatalogue 
     */
    public function createCatalogue($identifier = null)
    {
        if (strpos($identifier, '.') === false) {
            $old = clone $this->getIdentifier();
            
            if ($identifier) {
                $old->path = array('translator', 'catalogue');
                $old->name = $identifier;
            } else {
                $old->path = array('translator');
                $old->name = 'catalogue';
            }
            
            $identifier = $old;
        }

        return $this->getService($identifier);
    }
    
    public function getAliasCatalogue()
    {
        return $this->_alias_catalogue;
    }
    
    public function setAliasCatalogue($catalogue)
    {
        if ($catalogue instanceof KTranslatorCatalogueInterface) {
            $this->_alias_catalogue = $catalogue;
        } else {
            throw new KTranslatorException('Catalogues must implement KTranslatorCatalogueInterface');
        }
        
        return $this;
    }
    
    public function getDefaultCatalogue()
    {
        return $this->_catalogue;
    }
    
    public function setDefaultCatalogue($catalogue)
    {
        if ($catalogue instanceof KTranslatorCatalogueInterface) {
            $this->_catalogue = $catalogue;
        } else {
            throw new KTranslatorException('Catalogues must implement KTranslatorCatalogueInterface');
        }
    
        return $this;
    }
    
    public function getTranslator()
    {
        return $this->_translator;
    }
    
    public function setTranslator($translator)
    {
        if (is_object($translator)) {
            $this->_translator = $translator;
        } else {
            throw new KTranslatorException('Invalid translator');
        }
        
        return $this;
    }
    
    public function getPrefix()
    {
        return $this->_prefix;
    }
    
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    
        return $this;
    }
}