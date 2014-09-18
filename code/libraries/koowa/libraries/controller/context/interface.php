<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Context Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Context
 */
interface KControllerContextInterface extends KCommandInterface
{
    /**
     * Get the request object
     *
     * @return KControllerRequestInterface
     */
    public function getRequest();

    /**
     * Get the response object
     *
     * @return KControllerResponseInterface
     */
    public function getResponse();

    /**
     * Get the user object
     *
     * @return KUserInterface
     */
    public function getUser();

    /**
     * Get the controller action
     *
     * @return string
     */
    public function getAction();
}