<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
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
            'translations' => array()
        ));

        $translations = KObjectConfig::unbox($config->translations);
        $translator   = $this->getTemplate()->getTranslator();

        foreach ($translations as $string) {
            $translator->addScriptTranslation($string);
        }

        return '';
    }
}
