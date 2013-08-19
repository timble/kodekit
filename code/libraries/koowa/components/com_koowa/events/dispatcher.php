<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Event Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaEventDispatcher extends KEventDispatcher implements KServiceInstantiatable
{
 	/**
     * Force creation of a singleton
     *
     * @param 	KObjectConfigInterface $config	    An optional KObjectConfig object with configuration options
     * @param 	KServiceInterface $container	A KServiceInterface object
     * @return ComKoowaEventDispatcher
     */
    public static function getInstance(KObjectConfigInterface $config, KServiceInterface $container)
    {
       // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);

            //Add the factory map to allow easy access to the singleton
            $container->setAlias('koowa:event.dispatcher', $config->service_identifier);
        }

        return $container->get($config->service_identifier);
    }
}
