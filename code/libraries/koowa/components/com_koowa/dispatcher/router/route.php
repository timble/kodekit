<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher Route
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class ComKoowaDispatcherRouterRoute extends KDispatcherRouterRoute
{
    public function toString($parts = self::FULL)
    {
        $query = $this->getQuery(true);

        //Add the option to the query for compatibility with the Joomla router
        if(isset($query['component']))
        {
            if(!isset($this->query['option'])) {
                $query['option'] = 'com_'.$query['component'];
            }

            unset($query['component']);
        }

        //Push option and view to the beginning of the array for easy to read URLs
        $query = array_merge(array('option' => null, 'view'   => null), $query);

        //Let Joomla build the route
        $route = JRoute::_('index.php?'.http_build_query($query), $this->_escape);

        //Create a fully qualified route
        if(!empty($this->host) && !empty($this->scheme)) {
            $route = $authority = parent::toString(self::AUTHORITY) . '/' . ltrim($route, '/');
        }

        return $route;
    }
}