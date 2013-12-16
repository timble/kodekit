<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher User
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaControllerUserJoomla extends ComKoowaUser implements KControllerUserInterface
{
    /**
     * Set the request object
     *
     * @param KControllerRequestInterface $request A request object
     * @return KControllerUser
     */
    public function setRequest(KControllerRequestInterface $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get the request object
     *
     * @return KControllerRequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get a user attribute
     *
     * Implements a virtual 'session' class property to return the session object.
     *
     * @param   string $name  The attribute name.
     * @return  string $value The attribute value.
     */
    public function __get($name)
    {
        if($name == 'session') {
            return $this->getSession();
        }

        return parent::__get($name);
    }
}