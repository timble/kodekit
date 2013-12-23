<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher Request Singleton
 *
 * Force the user object to a singleton with identifier with alias 'request'.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class KDispatcherRequest extends KDispatcherRequestAbstract implements KObjectInstantiable, KObjectSingleton
{
    /**
     * Force creation of a singleton
     *
     * @param 	KObjectConfigInterface  $config	  A ObjectConfig object with configuration options
     * @param 	KObjectManagerInterface	$manager  A ObjectInterface object
     * @return KDispatcherRequest
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        if (!$manager->isRegistered('dispatcher.request'))
        {
            $classname = $config->object_identifier->classname;
            $instance  = new $classname($config);
            $manager->setObject($config->object_identifier, $instance);

            //Add the service alias to allow easy access to the singleton
            $manager->registerAlias($config->object_identifier, 'dispatcher.request');
            $manager->registerAlias('dispatcher.request', 'request');
        }

        return $manager->getObject('dispatcher.request');
    }
}