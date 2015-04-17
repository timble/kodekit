<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Html View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
class KViewHtml extends KViewTemplate
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'mimetype'          => 'text/html',
            'template_filters'  => array('form'),
        ));

        parent::_initialize($config);
    }

    /**
     * Force the route to be not fully qualified and escaped
     *
     * @param string|array  $route  The query string used to create the route
     * @param boolean       $fqr    If TRUE create a fully qualified route. Default FALSE.
     * @param boolean       $escape If TRUE escapes the route for xml compliance. Default TRUE.
     * @return  KDispatcherRouterRoute The route
     */
    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        return parent::getRoute($route, $fqr, $escape);
    }
}
