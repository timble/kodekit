<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator Interface
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Translator
 */
interface KTranslatorInterface
{
    /**
     * Translates a string and handles parameter replacements
     *
     * Parameters are wrapped in curly braces. So {foo} would be replaced with bar given that $parameters['foo'] = 'bar'
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
     */
    public function translate($string, array $parameters = array());

    /**
     * Translates a string based on the number parameter passed
     *
     * @param array   $strings Strings to choose from
     * @param integer $number The umber of items
     * @param array   $parameters An array of parameters
     * @throws InvalidArgumentException
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array());

    /**
     * Loads translations from a url
     *
     * @param string $url      The translation url
     * @param bool   $override If TRUE override previously loaded translations. Default FALSE.
     * @return bool True if translations were loaded, false otherwise
     */
    public function load($url, $override = false);

    /**
     * Find translations from a url
     *
     * @param string $url      The translation url
     * @return array An array with physical file paths
     */
    public function find($url);

    /**
     * Sets the locale
     *
     * @param string $locale
     * @return KTranslatorInterface
     */
    public function setLocale($locale);

    /**
     * Gets the locale
     *
     * @return string|null
     */
    public function getLocale();

    /**
     * Set the fallback locale
     *
     * @param string $locale The fallback locale.
     * @return KTranslatorInterface
     */
    public function setLocaleFallback($locale);

    /**
     * Get the fallback locale
     *
     * @return string The fallback locale.
     */
    public function getLocaleFallback();

    /**
     * Get a catalogue
     *
     * @throws  UnexpectedValueException    If the catalogue doesn't implement the TranslatorCatalogueInterface
     * @return KTranslatorCatalogueInterface The translator catalogue.
     */
    public function getCatalogue();

    /**
     * Set a catalogue
     *
     * @param   mixed   $catalogue An object that implements KObjectInterface, KObjectIdentifier object
     *                             or valid identifier string
     * @return KTranslatorInterface
     */
    public function setCatalogue($catalogue);

    /**
     * Checks if the translator can translate a string
     *
     * @param $string String to check
     * @return bool
     */
    public function isTranslatable($string);

    /**
     * Checks if translations from a given url are already loaded.
     *
     * @param mixed $url The url to check
     * @return bool TRUE if loaded, FALSE otherwise.
     */
    public function isLoaded($url);
}