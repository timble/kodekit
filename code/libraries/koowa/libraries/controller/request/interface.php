<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller Request Interface
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
     * @return KControllerRequestInterface
     */
    public function setQuery($query);

    /**
     * Get the request query
     *
     * @return KHttpMessageParameters
     */
    public function getQuery();

    /**
     * Set the request data
     *
     * @param  array $data
     * @return KControllerRequestInterface
     */
    public function setData($data);

    /**
     * Get the request data
     *
     * @return KHttpMessageParameters
     */
    public function getData();

    /**
     * Set the request format
     *
     * @param $format
     * @return KControllerRequestInterface
     */
    public function setFormat($format);

    /**
     * Return the request format
     *
     * @return  string  The request format or NULL if no format could be found
     */
    public function getFormat();
}