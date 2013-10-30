<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
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
     * Handles parameter replacements
     *
     * @param string $string String
     * @param array  $parameters An array of parameters
     * @return string String after replacing the parameters
     */
    public function replaceParameters($string, array $parameters = array());

    /**
     * Translates a string based on the number parameter passed
     *
     * @param array   $strings Strings to choose from
     * @param integer $number The umber of items
     * @param array   $parameters An array of parameters
     *
     * @throws InvalidArgumentException
     *
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array());

    /**
     * Checks if a given string is translatable.
     *
     * @param string $string The string to check.
     * @return bool True if it is, false otherwise.
     */
    public function isTranslatable($string);

    /**
     * Sets the locale
     *
     * @param string $locale
     * @return KTranslator
     */
    public function setLocale($locale);

    /**
     * Gets the locale
     *
     * @return string|null
     */
    public function getLocale();
}
