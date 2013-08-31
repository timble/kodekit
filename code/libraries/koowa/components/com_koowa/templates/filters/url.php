<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Url Template Filter
 *
 * Filter allows to create url aliases that are replaced on compile and render.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class ComKoowaTemplateFilterUrl extends KTemplateFilterUrl
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
            'aliases' => array(
                'media://' => KRequest::root().'/media/',
                'base://'  => KRequest::base().'/',
                'root://'  => KRequest::root().'/',
            ),
        ));

        parent::_initialize($config);
    }
}