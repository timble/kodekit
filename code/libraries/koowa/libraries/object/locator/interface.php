<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Locator Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectLocatorInterface
{
	/**
	 * Get the classname based on an identifier
	 *
	 * @param 	KObjectIdentifier $identifier An identifier object - [application::]type.package.[.path].name
	 * @return 	string|boolean 	Returns the class on success, returns FALSE on failure
	 */
	public function findClass(KObjectIdentifier $identifier);

	 /**
     * Get the path based on an identifier
     *
     * @param  KObjectIdentifier $identifier  An identifier object - [application::]type.package.[.path].name
     * @return string	Returns the path
     */
    public function findPath(KObjectIdentifier $identifier);

	/**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType();
}
