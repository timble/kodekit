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
 * Object Instantiable Interface
 *
 * The interface signals the ObjectManager to delegate object instantiation.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object
 * @see     ObjectManager::getObject()
 */
interface ObjectInstantiable
{
    /**
     * Instantiate the object
     *
     * @param   ObjectConfigInterface $config      Configuration options
     * @param   ObjectManagerInterface $manager    A ObjectManagerInterface object
     * @return  ObjectInterface
     */
    public static function getInstance(ObjectConfigInterface $config, ObjectManagerInterface $manager);
}
