<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
interface KControllerRequestInterface
{
    /**
     * Set the request query
     *
     * @param  array $query
     * @return $this
     */
    public function setQuery($query);

    /**
     * Get the request query
     *
     * @return KObjectArray
     */
    public function getQuery();
}