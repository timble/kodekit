<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator Helper
 *
 * Adds translation keys used in JavaScript to the translator object
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Template\Helper
 */
class KTemplateHelperTranslator extends KTemplateHelperAbstract
{
    public function script($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'strings' => array()
        ));

        $strings    = KObjectConfig::unbox($config->strings);
        $translator = $this->getObject('translator');

        $translations = array();
        foreach ($strings as $string) {
            $translations[$string] = $translator->translate($string);
        }

        $html  = '';
        $html .= $this->getTemplate()->helper('behavior.koowa') .
            "<script>
            if (typeof Koowa === 'object' && Koowa !== null) {
                if (typeof Koowa.translator === 'object' && Koowa.translator !== null) {
                    Koowa.translator.loadTranslations(".json_encode($translations).");
                }
            }
            </script>
            ";

        return $html;
    }
}
