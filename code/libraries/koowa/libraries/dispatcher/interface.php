<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
interface KDispatcherInterface
{
	/**
	 * Method to get a controller object
	 *
	 * @return	KControllerAbstract
	 */
	public function getController();

	/**
	 * Method to set a controller object attached to the dispatcher
	 *
	 * @param	mixed	$controller An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a controller identifier
	 * @return	KDispatcherAbstract
	 */
	public function setController($controller);
}
