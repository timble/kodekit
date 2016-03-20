<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Controller Context Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller\Context
 */
interface ControllerContextInterface extends CommandInterface
{
    /**
     * Get the request object
     *
     * @return ControllerRequestInterface
     */
    public function getRequest();

    /**
     * Get the response object
     *
     * @return ControllerResponseInterface
     */
    public function getResponse();

    /**
     * Get the user object
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Get the controller action
     *
     * @return string
     */
    public function getAction();
}