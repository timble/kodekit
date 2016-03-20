<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Default Model Entity
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Entity
 */
final class ModelEntityDefault extends ModelEntityAbstract implements ObjectInstantiable
{
    /**
     * Create an entity or a collection instance
     *
     * @param  ObjectConfigInterface   $config   A ObjectConfig object with configuration options
     * @param  ObjectManagerInterface  $manager  A ObjectInterface object
     * @return EventPublisher
     */
    public static function getInstance(ObjectConfigInterface $config, ObjectManagerInterface $manager)
    {
        $name = $config->object_identifier->name;

        if(StringInflector::isSingular($name)) {
            $class = __NAMESPACE__.'\ModelEntityRow';
        } else {
            $class = __NAMESPACE__.'\ModelEntityRowset';
        }

        $instance = new $class($config);
        return $instance;
    }
}