<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Template Template Filter
 *
 * Filter for the @import alias for partial template identifiers
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterTemplate extends KTemplateFilterAbstract implements KTemplateFilterCompiler
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => KTemplateFilter::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Replace partial template identifiers with full identifiers relative to the current layout.
     *
     * e.g. @import('foo') will be converted to @import('com://app/component.view.current_view.foo')
     *
     * @param string $text
     * @return KTemplateFilterTemplate
     */
    public function compile(&$text)
    {
        if(preg_match_all('#@import\(\'(.*)\'#siU', $text, $matches))
        {
            foreach($matches[1] as $key => $match)
            {
                if (strpos($match, '.') === false)
                {
                    $identifier = clone $this->getTemplate()->getView()->getIdentifier();
                    $identifier->name = $match;

                    $text = str_replace($matches[0][$key], '@import('."'".$identifier."'", $text);
                }
            }
        }

        return $this;
    }
}
