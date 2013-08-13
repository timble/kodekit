<?php
/**
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Locator Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Service
 */
interface KServiceLocatorInterface
{
	/**
	 * Get the classname based on an identifier
	 *
	 * @param 	KServiceIdentifier $identifier An identifier object - [application::]type.package.[.path].name
	 * @return 	string|boolean 	Returns the class on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier);

	 /**
     * Get the path based on an identifier
     *
     * @param  KServiceIdentifier $identifier  An identifier object - [application::]type.package.[.path].name
     * @return string	Returns the path
     */
    public function findPath(KServiceIdentifier $identifier);

	/**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType();
}
