<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Markdown Template Filter
 *
 * Filter to parse <ktml:template:[engine]></ktml:template:[engine]> tags. Content and will be rendered using the
 * specific engine.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
class TemplateFilterTemplate extends TemplateFilterAbstract
{
    /**
     * Replace <ktml:template:[format]></ktml:template:[format]> and rendered the content
     *
     * @param string $text  The text to parse
     * @param TemplateInterface $template A template object.
     * @return void
     */
    public function filter(&$text, TemplateInterface $template)
    {
        $factory = $this->getObject('template.engine.factory');

        $types = $factory->getFileTypes();
        $types = implode('|', $types);

        $matches = array();
        if(preg_match_all('#<ktml:template:('.$types.')>(.*)<\/ktml:template:('.$types.')>#siU', $text, $matches))
        {
            foreach($matches[0] as $key => $match)
            {
                $data = $template->getData();
                $html = $factory->createEngine($matches[1][$key], array('functions' => $template->getFunctions()))
                    ->render($matches[2][$key], $data);

                $text = str_replace($match, $html, $text);
            }
        }
    }
}
