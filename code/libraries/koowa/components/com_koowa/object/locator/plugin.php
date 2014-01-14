<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Plugin Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaObjectLocatorPlugin extends KObjectLocatorAbstract
{
    /**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'plg';

    /**
     * Returns a fully qualified class name for a given identifier.
     *
     * @param KObjectIdentifier $identifier An identifier object
     * @param bool  $fallback   Use the fallbacks to locate the identifier
     * @return string|false  Return the class name on success, returns FALSE on failure
     */
    public function locate(KObjectIdentifier $identifier, $fallback = true)
    {
	    $classpath = KStringInflector::camelize(implode('_', $identifier->path));
		$classname = 'Plg'.ucfirst($identifier->package).$classpath.ucfirst($identifier->name);

		//Don't allow the auto-loader to load plugin classes if they don't exists yet
		if (!class_exists( $classname)) {
			$classname = false;
		}

		return $classname;
	}
}
