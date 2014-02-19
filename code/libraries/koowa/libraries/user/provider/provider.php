<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * User Provider Singleton
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User
 */
class KUserProvider extends KUserProviderAbstract implements KObjectInstantiable, KObjectSingleton
{
    /**
     * Force creation of a singleton
     *
     * @param  KObjectConfig            $config	  A KObjectConfig object with configuration options
     * @param  KObjectManagerInterface	$manager  A KObjectInterface object
     * @return KUserProvider
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        if (!$manager->isRegistered('user.provider'))
        {
            $class     = $manager->getClass($config->object_identifier);
            $instance  = new $class($config);
            $manager->setObject($config->object_identifier, $instance);

            $manager->registerAlias($config->object_identifier, 'user.provider');
        }

        return $manager->getObject('user.provider');
    }
}