<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
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

            foreach($this->find($url) as $extension => $base)
            {
                $loaded[] =  JFactory::getLanguage()->load($extension, $base, $fallback, true, false);

                if ($this->getLocale() !== $this->getLocaleFallback()) {
                    $loaded[] =  JFactory::getLanguage()->load($extension, $base, $locale, true, false);
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