<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Dispatcher Route
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Dispatcher\Router
 */
class ComKoowaDispatcherRouterRoute extends KDispatcherRouterRoute
{
    public function toString($parts = self::FULL, $escape = null)
    {
        $query  = $this->getQuery(true);
        $escape = isset($escape) ? $escape : $this->_escape;

        //Add the option to the query for compatibility with the Joomla router
        if(isset($query['component']))
        {
            if(!isset($query['option'])) {
                $query['option'] = 'com_'.$query['component'];
            }

            unset($query['component']);
        }

        if (isset($query['format']) && JFactory::getApplication()->getCfg('sef_suffix'))
        {
            // Convert format=rss to format=feed for compatibility with the Joomla router
            if ($query['format'] === 'rss') {
                $query['format'] = 'feed';
            }
            // Make sure .htaccess file can handle the format. Only a handful of formats are allowed before 3.3.1
            else
            {
                $allowed = array('php', 'html', 'htm', 'feed', 'pdf', 'vcf', 'raw');

                if (!in_array($query['format'], $allowed))
                {
                    $append_format = $query['format'];
                    $query['format'] = 'raw';
                }
            }
        }

        // Add the 'tmpl' information to the route if a 'tmpl' is set in the request
        if (!isset($query['tmpl']) && $tmpl = $this->getObject('request')->getQuery()->get('tmpl', 'cmd')) {
            $query['tmpl'] = $tmpl;
        }

        //Push option and view to the beginning of the array for easy to read URLs
        $query = array_merge(array('option' => null, 'view'   => null), $query);

        //Let Joomla build the route
        $route = JRoute::_('index.php?'.http_build_query($query, '', '&'), $escape);

        // We had to change the format in the URL above so that .htaccess file can catch it
        if (isset($append_format)) {
            $route .= (strpos($route, '?') !== false ? '&' : '?').'format='.$append_format;
        }

        //Create a fully qualified route
        if(!empty($this->host) && !empty($this->scheme)) {
            $route = parent::toString(self::AUTHORITY) . '/' . ltrim($route, '/');
        }

        return $route;
    }
}