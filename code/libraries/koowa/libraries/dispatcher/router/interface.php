<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Dispatcher Router
 *
 * Provides route building and parsing functionality
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
interface KDispatcherRouterInterface
{
    /**
     * Function to convert a route to an internal URI
     *
     * @param   KHttpUrlInterface  $url  The url.
     * @return  boolean
     */
	public function parse(KHttpUrlInterface $url);

    /**
     * Function to convert an internal URI to a route
     *
     * @param	KHttpUrl   $url	The internal URL
     * @return	boolean
     */
	public function build(KHttpUrlInterface $url);
}
