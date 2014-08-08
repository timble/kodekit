<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translation Template Filter
 *
 * Used to pass translation keys in JavaScript
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Template
 */
class KTemplateFilterTranslator extends KTemplateFilterAbstract implements KTemplateFilterRenderer
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    public function render(&$text)
    {
        $translations = $this->getObject('translator')->getScriptCatalogue()->toArray();

        if (count($translations))
        {
            $text .= $this->getTemplate()->invokeHelper('behavior.koowa') .
            "<script>
            if (typeof Koowa === 'object' && Koowa !== null) {
                if (typeof Koowa.translator === 'object' && Koowa.translator !== null) {
                    Koowa.translator.loadTranslations(".json_encode($translations).");
                }
            }
            </script>
            ";
        }
    }
}