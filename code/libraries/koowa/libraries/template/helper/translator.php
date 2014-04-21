<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Translator Helper Class
 *
 * Adds translation keys used in JavaScript to the translator object
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class KTemplateHelperTranslator extends KTemplateHelperAbstract
{
    public function script($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'translations' => array(
                'Bytes', 'KB', 'MB', 'GB', 'TB', 'PB',
                'An error occurred during request',
                'You are deleting {item}. Are you sure?',
                'You are deleting {items}. Are you sure?',
                '{count} files and folders',
                '{count} folders',
                '{count} files',
                'All Files',
                'An error occurred with status code: ',
                'An error occurred: ',
                'Unknown error',
                'Uploaded successfully!',
                'Select files from your computer',
                'Choose File'
            )
        ));

        $translations = KObjectConfig::unbox($config->translations);
        $translator   = $this->getTemplate()->getTranslator();

        foreach ($translations as $string) {
            $translator->addScriptTranslation($string);
        }

        return '';
    }
}
