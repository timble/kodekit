<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller User Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
interface KControllerUserInterface extends KUserInterface
{
    /**
     * Set the request object
     *
     * @param KControllerRequestInterface $request A request object
     * @return KControllerUserInterface
     */
    public function setRequest(KControllerRequestInterface $request);

    /**
     * Get the request object
     *
     * @return KControllerRequestInterface
     */
    public function getRequest();
}