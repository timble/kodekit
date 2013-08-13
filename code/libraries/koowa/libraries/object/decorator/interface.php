<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Decorator Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectDecoratorInterface
{
	/**
	 * Get the decorated object
	 *
	 * @return	object The decorated object
	 */
	public function getObject();

	/**
	 * Set the decorated object
	 *
	 * @param 	object
	 * @return 	$this
	 */
	public function setObject($object);
}
