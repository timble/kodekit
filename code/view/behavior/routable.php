<?php
/**
 * Kodekit Component - http://www.timble.net/kodekit
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link		https://github.com/timble/kodekit-pages for the canonical source repository
 */

namespace  Kodekit\Library;

/**
 * Routable View Behavior
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\View\Behavior
 */
class ViewBehaviorRoutable extends ViewBehaviorAbstract
{
    /**
     * Get a route based on a full or partial query string
     *
     * 'option', 'view' and 'layout' can be omitted. The following variations will all result in the same route :
     *
     * - foo=bar
     * - component=[package]&view=[name]&foo=bar
     *
     * In templates, use route()
     *
     * @param   string|array $route  The query string or array used to create the route
     * @param   boolean      $fqr    If TRUE create a fully qualified route. Defaults to TRUE.
     * @param   boolean      $escape If TRUE escapes the route for xml compliance. Defaults to TRUE.
     * @return  DispatcherRouterRoute The route
     */
    public function getRoute($route = '', $fqr = true)
    {
        $parts      = array();
        $identifier = $this->getMixer()->getIdentifier();

        if(is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        //Check to see if there is component information in the route if not add it
        if (!isset($parts['component'])) {
            $parts['component'] = $identifier->package;
        }

        //Add the view information to the route if it's not set
        if (!isset($parts['view'])) {
            $parts['view'] = $this->getName();
        }

        //Add the format information to the route only if it's not 'html'
        if (!isset($parts['format']) && $identifier->name !== 'html') {
            $parts['format'] = $identifier->name;
        }

        //Add the model state and layout only for routes to the same view
        if ($parts['component'] == $identifier->package && $parts['view'] == $this->getName())
        {
            $states = array();
            foreach($this->getModel()->getState() as $name => $state)
            {
                if($state->default != $state->value && !$state->internal) {
                    $states[$name] = $state->value;
                }
            }

            $parts = array_merge($states, $parts);

            //Add the layout information
            if(!isset($parts['layout']) && $this->getMixer() instanceof ViewTemplatable)
            {
                $layout = $this->getLayout();

                if ($layout !== 'default') {
                    $parts['layout'] = $layout;
                }
            }
        }

        //Create the route
        $escape = $this->getUrl()->isEscaped();
        $route  = $this->getObject('lib:dispatcher.router.route', array('escape' =>  $escape))->setQuery($parts);

        //Add the host and the schema
        if ($fqr === true)
        {
            $route->scheme = $this->getUrl()->scheme;
            $route->host   = $this->getUrl()->host;
        }

        return $route;
    }
}