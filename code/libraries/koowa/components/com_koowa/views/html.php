<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Html View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class ComKoowaViewHtml extends KViewHtml
{
    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Add alias filter for editor helper
        $this->getTemplate()->getFilter('function')->addFunction('@editor', '$this->renderHelper(\'editor.display\', ');
    }

    /**
     * Creates a route based on a full or partial query string.
     *
     * This function adds the 'tmpl' information to the route if a 'tmpl' has been set
     *
     * @param string|array $route   The query string used to create the route
     * @param boolean $fqr          If TRUE create a fully qualified route. Default TRUE.
     * @param boolean $escape       If TRUE escapes the route for xml compliance. Default TRUE.
     * @return KHttpUrl             The route
     */
    public function getRoute($route = '', $fqr = true, $escape = true)
    {
        if(is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        if (!isset($parts['tmpl']) && $tmpl = $this->getObject('request')->getQuery()->get('tmpl', 'cmd')) {
            $parts['tmpl'] = $tmpl;
        }

        return parent::getRoute($parts, $fqr, $escape);
    }
}
