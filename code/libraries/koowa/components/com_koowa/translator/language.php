<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator Language Class
 *
 * Extends JLanguage for accessing protected properties.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Koowa\Translator
 */
class ComKoowaTranslatorLanguage extends JLanguage
{
    /**
     * Associative array containing the list of loaded translations.
     *
     * @var array
     */
    static protected $_paths;

    /**
     * Adds file translations to the JLanguage catalogue.
     *
     * @param string               $file       The file containing translations.
     * @param string               $extension  The name of the extension containing the file.
     * @param KTranslatorInterface $translator The Translator object.
     *
     * @return bool True if translations where loaded, false otherwise.
     */
    static public function loadFile($file, $extension, KTranslatorInterface $translator)
    {
        $lang     = JFactory::getLanguage();
        $result   = false;

        if (!isset(self::$_paths[$extension][$file]))
        {
            $strings = self::parseFile($file, $translator);

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

            self::$_paths[$extension][$file] = $result;
        }

        return $result;
    }

    /**
     * Parses a translations file and returns an array of key/values entries.
     *
     * @param string               $file       The file to parse.
     * @param KTranslatorInterface $translator The translator object.
     * @return array The parse result.
     */
    static public function parseFile($file, KTranslatorInterface $translator)
    {
        $strings   = array();
        $catalogue = $translator->getCatalogue();

        // Catch exceptions if any.
        try {
            $translations = $translator->getObject('object.config.factory')->fromFile($file);
        }  catch (Exception $e) {
            $translations = array();
        }

        foreach ($translations as $key => $value) {
            $strings[$catalogue->getPrefix() . $catalogue->generateKey($key)] = $value;
        }

        return $strings;
    }
}