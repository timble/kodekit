<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Context
 */
class KControllerContext extends KCommand implements KControllerContextInterface
{
    /**
     * Get the request object
     *
     * @return KControllerRequestInterface
     */
    public function getRequest()
    {
        return KObjectConfig::get('request');
    }

    /**
     * Set the request object
     *
     * @param KControllerRequestInterface $request
     * @return KControllerContext
     */
    public function setRequest(KControllerRequestInterface $request)
    {
        return KObjectConfig::set('request', $request);
    }

    /**
     * Get the response object
     *
     * @return KControllerResponseInterface
     */
    public function getResponse()
    {
        return KObjectConfig::get('response');
    }

    /**
     * Set the response object
     *
     * @param KControllerResponseInterface $response
     * @return KControllerContext
     */
    public function setResponse(KControllerResponseInterface $response)
    {
        return KObjectConfig::set('response', $response);
    }

    /**
     * Get the user object
     *
     * @return KUserInterface
     */
    public function getUser()
    {
        return KObjectConfig::get('user');
    }

    /**
     * Set the user object
     *
     * @param KUserInterface $user
     * @return $this
     */
    public function setUser(KUserInterface $user)
    {
        return KObjectConfig::set('user', $user);
    }

    /**
     * Get the controller action
     *
     * @return string
     */
    public function getAction()
    {
        return KObjectConfig::get('action');
    }

    /**
     * Set the controller action
     *
     * @param string $action
     * @return KControllerContext
     */
    public function setAction($action)
    {
        return KObjectConfig::set('action', $action);
    }
}