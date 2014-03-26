<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

/**
 * Default Model Entity
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package NookuLibraryModel
 */
final class KModelEntityDefault extends KModelEntityAbstract implements KObjectInstantiable
{
    /**
     * Create an entity or a collection instance
     *
     * @param  KObjectConfigInterface   $config	  A KObjectConfig object with configuration options
     * @param  KObjectManagerInterface	$manager  A ObjectInterface object
     * @return KEventPublisher
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        $name = $config->object_identifier->name;

        if(KStringInflector::isSingular($name)) {
            $class = 'KModelEntityRow';
        } else {
            $class = 'KModelEntityRowset';
        }

        $instance = new $class($config);
        return $instance;
    }
}