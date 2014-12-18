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
            $current  = $this->getLocale();
            $fallback = $this->getLocaleFallback();

            foreach($this->find($url) as $extension => $base)
            {
                $locales   = array($current);

                if ($current !== $fallback) {
                    array_unshift($locales, $fallback);
                }

                foreach ($locales as $locale)
                {
                    if (!JFactory::getLanguage()->load($extension, $base, $locale, true, false))
                    {
                        $file = glob(sprintf('%s/language/%s.*', $base, $locale));

                        if ($file) {
                            $loaded[] = ComKoowaTranslatorLanguage::loadFile(current($file), $extension, $this);
                        }
                    }
                    else $loaded[] = true;
                }
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