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
class ComKoowaEventDispatcher extends KEventDispatcher implements KObjectInstantiatable
{
 	/**
     * Force creation of a singleton
     *
     * @param 	KObjectConfigInterface $config	    An optional KObjectConfig object with configuration options
     * @param 	KObjectManagerInterface $manager	A KObjectManagerInterface object
     * @return ComKoowaEventDispatcher
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
       // Check if an instance with this identifier already exists or not
        if (!$manager->isRegistered($config->object_identifier))
        {
            //Create the singleton
            $classname = $config->object_identifier->classname;
            $instance  = new $classname($config);
            $manager->setObject($config->object_identifier, $instance);

            //Add the factory map to allow easy access to the singleton
            $manager->registerAlias('koowa:event.dispatcher', $config->object_identifier);
        }

        return $manager->getObject($config->object_identifier);
    }
}
