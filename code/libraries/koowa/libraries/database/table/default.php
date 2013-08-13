<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Default Database Table
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KDatabaseTableDefault extends KDatabaseTableAbstract implements KServiceInstantiatable
{
	/**
     * Force creation of a singleton
     *
     * @param   KConfigInterface    $config    Configuration options
     * @param 	KServiceInterface	$container A KServiceInterface object
     * @return KDatabaseTableDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }
}
