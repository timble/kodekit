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
class ComDefaultTranslator extends KTranslator implements KServiceInstantiatable
{
    /**
     * A reference to Joomla translator
     * @var object
     */
    protected $_translation_helper;

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
     * Fallback locale to always load the language files from
     * @var string
     */
    protected $_fallback_locale;

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

    /**
     * @param KConfig $config
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if ($config->fallback_locale) {
            $this->_fallback_locale = $config->fallback_locale;
        }

        $this->setTranslationHelper($config->translation_helper);
        $this->setPrefix($config->prefix);

        $this->setDefaultCatalogue($this->createCatalogue($config->catalogue));
        $this->setAliasCatalogue($this->createCatalogue($config->alias_catalogue));
    }

    /**
     * @param KConfig $config
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'prefix'     => 'KLS_',
            'catalogue'  => null,
            'alias_catalogue'    => 'aliases',
            'fallback_locale'    => 'en-GB',
            'locale'             => JFactory::getConfig()->get('language'),
            'translation_helper' => JFactory::getLanguage()
        ));

        parent::_initialize($config);
    }

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     *
     * @return string Translated string
     */
    public function translate($string, array $parameters = array())
    {
        $result = strtolower($string);

        if (empty($result)) {
            $result = '';
        }
        elseif (isset($this->_alias_catalogue[$result])) {
            $result = $this->_translation_helper->_($this->_alias_catalogue[$result]);
        }
        else {
            if (substr($string, 0, strlen($this->_prefix)) === $this->_prefix) {
                $key = $string;
            } else {
                $key = $this->getKey($string);
            }

            $result = $this->_translation_helper->_($this->_translation_helper->hasKey($key) ? $key : $string);
        }

        // Joomla uses _QQ_ instead of " in language files
        // and 1.5 does not handle the conversion itself
        if (version_compare(JVERSION, '1.6', '<')) {
            $result = str_replace('"_QQ_"', '"', $result);
        }

        return parent::translate($result, $parameters);
    }

    /**
     * Translates a string based on the number parameter passed
     *
     * @param array   $strings    Strings to choose from
     * @param integer $number     The umber of items
     * @param array   $parameters An array of parameters
     *
     * @throws InvalidArgumentException
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array())
    {
        if (count($strings) < 2) {
            throw new InvalidArgumentException('Choose method requires at least 2 strings to choose from');
        }

        $choice = KTranslatorPluralizationrules::get($number, $this->_locale);

        if ($choice === 0) {
            return $this->translate($strings[0], $parameters);
        }

        $key = $this->getKey($strings[1]);
        $found = null;
        while ($choice > 0) {
            $looking_for = $key.($choice === 1 ? '' : '_'.$choice);
            if ($this->_translation_helper->hasKey($looking_for)) {
                $found = $looking_for;
                break;
            }

            $choice--;
        }

        return $this->translate($found ? $found : $strings[1], $parameters);
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
            $app        = $identifier->application === 'admin' ? 'administrator' : $identifier->application;
        } else {
            $type = substr($extension, 0, 3);
            $app  = null;
        }

        if ($base === null)
        {
            if ($app && defined('JPATH_'.strtoupper($app))) {
                $base = constant('JPATH_'.strtoupper($app));
            } else {
                $base = JPATH_BASE;
            }
        }

        if (isset(self::$_type_map[$type])) {
            $type = self::$_type_map[$type];
        } else {
            throw new KTranslatorException(sprintf('Invalid extension type: %s', $type));
        }

        $ext_base = sprintf('%s/%ss/%s', $base, $type, $extension);

        $results = array();
        $results[] = $this->_loadLanguageFile($extension, $this->_fallback_locale, array($ext_base, $base));

        if ($this->getLocale() !== $this->_fallback_locale) {
            $results[] = $this->_loadLanguageFile($extension, $this->getLocale(), array($ext_base, $base));
        }

        return in_array(true, $results);
    }

    /**
     * Loads a Joomla language file
     *
     * @param string $extension
     * @param string $locale Locale name
     * @param array  $base   Base path list
     *
     * @return bool
     */
    protected function _loadLanguageFile($extension, $locale, array $base)
    {
        $result = false;

        foreach ($base as $path) {
            $signature = md5($extension.$path.$locale);

            $result = $this->_translation_helper->load($extension, $path, $locale, true, false);
            // var_dump($extension, $locale, $path, $result);
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

    /**
     * Return the alias catalogue
     *
     * @return KTranslatorCatalogueInterface
     */
    public function getAliasCatalogue()
    {
        return $this->_alias_catalogue;
    }

    /**
     * Set the alias catalogue
     *
     * @param $catalogue
     *
     * @return $this
     */
    public function setAliasCatalogue(KTranslatorCatalogueInterface $catalogue)
    {
        $this->_alias_catalogue = $catalogue;

        return $this;
    }

    /**
     * Return the default catalogue
     *
     * @return KTranslatorCatalogueInterface
     */
    public function getDefaultCatalogue()
    {
        return $this->_catalogue;
    }

    /**
     * Set the default catalogue
     *
     * @param KTranslatorCatalogueInterface $catalogue
     *
     * @return $this
     */
    public function setDefaultCatalogue(KTranslatorCatalogueInterface $catalogue)
    {
        $this->_catalogue = $catalogue;

        return $this;
    }

    /**
     * Return translation helper
     *
     * @return object
     */
    public function getTranslationHelper()
    {
        return $this->_translation_helper;
    }

    /**
     * Set the translation helper
     *
     * @param object $translator
     *
     * @return $this
     * @throws KTranslatorException
     */
    public function setTranslationHelper($translator)
    {
        if (is_object($translator)) {
            $this->_translation_helper = $translator;
        } else {
            throw new KTranslatorException('Invalid translator');
        }

        return $this;
    }

    /**
     * Return the language key prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Set the language key prefix
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;

        return $this;
    }

    /**
     * Returns a translator object for a specific identifier
     *
     * @param KServiceIdentifier|string $identifier
     * @param KConfig|array             $config
     *
     * @throws KTranslatorException
     * @return KTranslator
     */
    public function getTranslator($identifier, $config = array()) {
        if (is_string($identifier)) {
            $translator = new KServiceIdentifier($identifier);
        }
        elseif ($identifier instanceof KServiceIdentifierInterface) {
            $translator = clone $identifier;
        }
        else {
            throw new KTranslatorException('Invalid identifier');
        }

        // If you omit the path in modules KServiceLocatorModule assumes it's a view. Hence:
        if ($translator->type === 'mod') {
            $translator->path = array('translator');
            $translator->name = '';
        } else {
            $translator->path = array();
            $translator->name = 'translator';
        }

        return $this->getService($translator, $config);
    }

    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface  $config optional KConfig object with configuration options
     * @param KServiceInterface $container
     *
     * @return  KTranslator
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }
}