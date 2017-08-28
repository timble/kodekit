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
 * Rss View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View
 */
class ViewRss extends ViewTemplate
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param ObjectConfig $config	An optional Object object with configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('localizable', 'routable'),
            'mimetype'  => 'application/rss+xml',
            'data'      => array(
                'update_period'    => 'hourly',
                'update_frequency' => 1
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Get the layout to use
     *
     * @return   string The layout name
     */
    public function getLayout()
    {
        return 'default';
    }

    /**
     * Prepend the xml prolog
     *
     * @param ViewContextTemplate  $context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(ViewContext $context)
    {
        //Prepend the xml prolog
        $content  = '<?xml version="1.0" encoding="utf-8" ?>';
        $content .=  parent::_actionRender($context);

        return $content;
    }

}