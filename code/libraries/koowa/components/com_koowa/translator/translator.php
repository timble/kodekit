<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa\Translator
 */
class ComKoowaTranslator extends KTranslator
{
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
            'locale'  => JFactory::getConfig()->get('language'),
        ));

        parent::_initialize($config);
    }

    /**
     * Prevent caching
     *
     * Do not decorate the translator with the cache.
     *
     * @param   KObjectConfigInterface  $config   A ObjectConfig object with configuration options
     * @param   KObjectManagerInterface	$manager  A ObjectInterface object
     * @return  $this
     * @see KFilterTraversable
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        $class = $manager->getClass($config->object_identifier);
        return new $class($config);
    }

    /**
     * Loads translations from a url
     *
     * @param string $url      The translation url
     * @param bool   $override If TRUE override previously loaded translations. Default FALSE.
     * @return bool TRUE if translations are loaded, FALSE otherwise
     */
    public function load($url, $override = false)
    {
        $loaded = array();

        if (!$this->isLoaded($url))
        {
            $locale       = $this->getLocale();
            $fallback     = $this->getLocaleFallback();

            foreach($this->find($url) as $extension => $file)
            {
                if (is_dir($file))
                {
                    $loaded[] =  JFactory::getLanguage()->load($extension, $file, $fallback, true, false);

                    if ($this->getLocale() !== $this->getLocaleFallback()) {
                        $loaded[] =  JFactory::getLanguage()->load($extension, $file, $locale, true, false);
                    }
                }
                else ComKoowaJLanguage::add($file, $extension, $this);
            }

            $this->_loaded[] = $url;
        }

        return in_array(true, $loaded);
    }

    /**
     * Sets the locale
     *
     * @param string $locale
     * @return KTranslatorAbstract
     */
    public function setLocale($locale)
    {
        if($this->_locale != $locale)
        {
            parent::setLocale($locale);

            //Load the koowa translations
            $this->load('com:koowa');
        }

        return $this;
    }
}

/**
 * Koowa JLanguage Class
 *
 * Extended for accessing protected JLanguage properties.
 */
class ComKoowaJLanguage extends JLanguage
{
    /**
     * Adds file translations to the JLanguage catalogue.
     *
     * @param  string              $file       The file containing translations.
     * @param                      $extension  The name of the extension containing the file.
     * @param KTranslatorInterface $translator The Translator object.
     */
    static public function add($file, $extension, KTranslatorInterface $translator)
    {
        $filename = basename($file);
        $lang     = JFactory::getLanguage();

        if (!isset($lang->paths[$extension][$filename]))
        {
            $lang->counter++;

            $strings   = array();
            $result    = false;
            $catalogue = $translator->getCatalogue();

            foreach ($translator->getObject('object.config.factory')->fromFile($file) as $key => $value) {
                $strings[$catalogue->getPrefix() . $catalogue->generateKey($key)] = $value;
            }

            if (count($strings))
            {
                ksort($strings, SORT_STRING);

                $lang->strings = array_merge($lang->strings, $strings);

                if (!empty($lang->override)) {
                    $lang->strings = array_merge($lang->strings, $lang->override);
                }

                $result = true;
            }

            // Record the result of loading the extension's file.
            if (!isset($lang->paths[$extension])) {
                $lang->paths[$extension] = array();
            }

            $lang->paths[$extension][$filename] = $result;
        }
    }
}