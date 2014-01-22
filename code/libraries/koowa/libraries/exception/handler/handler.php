<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Exception Handler
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Exception
 */
class KExceptionHandler extends KExceptionHandlerAbstract implements KObjectInstantiable, KObjectSingleton
{
    /**
     * Force creation of a singleton
     *
     * @param  KObjectConfigInterface   $config	  A ObjectConfig object with configuration options
     * @param  KObjectManagerInterface	$manager  A ObjectInterface object
     * @return KDispatcherRequest
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        if (!$manager->isRegistered('exception.handler'))
        {
            $class    = $manager->getClass($config->object_identifier);
            $instance = new $class($config);
            $manager->setObject($config->object_identifier, $instance);

            $manager->registerAlias($config->object_identifier, 'exception.handler');
        }

        return $manager->getObject('exception.handler');
    }
}