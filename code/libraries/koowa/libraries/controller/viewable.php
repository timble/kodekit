<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */
/**
 * Controller Viewable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
interface KControllerViewable
{
    /**
     * Get the controller view
     *
     * @throws	\UnexpectedValueException	If the view doesn't implement the ViewInterface
     * @return	KViewInterface
     */
    public function getView();

    /**
     * Set the controller view
     *
     * @param	mixed	$view   An object that implements ObjectInterface, ObjectIdentifier object
     * 					        or valid identifier string
     * @return	KControllerInterface
     */
    public function setView($view);

    /**
     * Get the supported formats
     *
     * @return array
     */
    public function getFormats();
}