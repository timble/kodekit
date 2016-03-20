<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Abstract Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Database\Behavior
 */
abstract class DatabaseBehaviorAbstract extends BehaviorAbstract implements ObjectInstantiable
{
    /**
     * Instantiate the object
     *
     * If the behavior is auto mixed also lazy mix it into related row objects.
     *
     * @param 	ObjectConfigInterface  $config	  A ObjectConfig object with configuration options
     * @param 	ObjectManagerInterface	$manager  A ObjectInterface object
     * @return  DatabaseBehaviorAbstract
     */
    public static function getInstance(ObjectConfigInterface $config, ObjectManagerInterface $manager)
    {
        $class    = $manager->getClass($config->object_identifier);
        $instance = new $class($config);

        //Lazy mix behavior into related row objects. A supported behavior always has one is[Behaviorable] method.
        if ($instance->isSupported() && $instance->getMixer() && count($instance->getMixableMethods()) > 1)
        {
            $identifier = $instance->getMixer()->getIdentifier()->toArray();
            $identifier['path'] = array('database', 'row');
            $identifier['name'] = StringInflector::singularize($identifier['name']);

            $manager->registerMixin($identifier, $instance);
        }

        return $instance;
    }

    /**
     * Command handler
     *
     * @param CommandInterface         $command    The command
     * @param CommandChainInterface    $chain      The chain executing the command
     * @return array|mixed Returns an array of the handler results in FIFO order. If a handler returns not NULL and the
     *                     returned value equals the break condition of the chain the break condition will be returned.
     */
    public function execute(CommandInterface $command, CommandChainInterface $chain)
    {
        if ($command->data instanceof DatabaseRowInterface) {
            $this->setMixer($command->data);
        }

        return parent::execute($command, $chain);
    }

	/**
     * Saves the row or rowset in the database.
     *
     * This function specialises the DatabaseRow or DatabaseRowset save function and auto-disables the tables command
     * chain to prevent recursive looping.
     *
     * @return DatabaseRowAbstract or DatabaseRowsetAbstract
     * @see DatabaseRow::save or DatabaseRowset::save
     */
    public function save()
    {
        $this->getTable()->getCommandChain()->setEnabled(false);
        $this->getMixer()->save();
        $this->getTable()->getCommandChain()->setEnabled(true);

        return $this->getMixer();
    }

    /**
     * Deletes the row form the database.
     *
     * This function specialises the DatabaseRow or DatabaseRowset delete function and auto-disables the tables command
     * chain to prevent recursive looping.
     *
     * @return DatabaseRowAbstract
     */
    public function delete()
    {
        $this->getTable()->getCommandChain()->setEnabled(false);
        $this->getMixer()->delete();
        $this->getTable()->getCommandChain()->setEnabled(true);

        return $this->getMixer();
    }

    /**
     * Get the methods that are available for mixin based
     *
     * @param  array $exclude   A list of methods to exclude
     * @return array  An array of methods
     */
    public function getMixableMethods($exclude = array())
    {
        $exclude = array_merge($exclude, array('getInstance', 'save', 'delete'));
        return parent::getMixableMethods($exclude);
    }
}
