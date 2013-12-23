<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * User Session Singleton
 *
 * Force the user object to a singleton
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User
 */
class KUserSession extends KUserSessionAbstract implements KObjectInstantiable, KObjectSingleton
{
    /**
     * Force creation of a singleton
     *
     * @param  KObjectConfig            $config	  A KObjectConfigInterface object with configuration options
     * @param  KObjectManagerInterface	$manager  A KObjectManagerInterface object
     * @return KDispatcherRequest
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        if (!$manager->isRegistered('user.session'))
        {
            $classname = $config->object_identifier->classname;
            $instance  = new $classname($config);
            $manager->setObject($config->object_identifier, $instance);

            $manager->registerAlias($config->object_identifier, 'user.session');
        }

        return $manager->getObject('user.session');
    }
}