<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Behavior
 */
abstract class KDatabaseBehaviorAbstract extends KBehaviorAbstract implements KObjectInstantiable
{
    /**
     * Instantiate the object
     *
     * If the behavior is auto mixed also lazy mix it into related row objects.
     *
     * @param 	KObjectConfigInterface  $config	  A KObjectConfig object with configuration options
     * @param 	KObjectManagerInterface	$manager  A KObjectInterface object
     * @return  KDatabaseBehaviorAbstract
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        $class    = $manager->getClass($config->object_identifier);
        $instance = new $class($config);

        //Lazy mix behavior into related row objects. A supported behavior always has one is[Behaviorable] method.
        if ($instance->isSupported() && $instance->getMixer() && count($instance->getMixableMethods()) > 1)
        {
            $identifier = $instance->getMixer()->getIdentifier()->toArray();
            $identifier['path'] = array('database', 'row');
            $identifier['name'] = KStringInflector::singularize($identifier['name']);

            $manager->registerMixin($identifier, $instance);
        }

        return $instance;
    }

    /**
     * Command handler
     *
     * @param KCommandInterface         $command    The command
     * @param KCommandChainInterface    $chain      The chain executing the command
     * @return array|mixed Returns an array of the handler results in FIFO order. If a handler returns not NULL and the
     *                     returned value equals the break condition of the chain the break condition will be returned.
     */
    public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        if ($command->data instanceof KDatabaseRowInterface) {
            $this->setMixer($command->data);
        }

        return parent::execute($command, $chain);
    }

	/**
     * Saves the row or rowset in the database.
     *
     * This function specialises the KDatabaseRow or KDatabaseRowset save function and auto-disables the tables command
     * chain to prevent recursive looping.
     *
     * @return KDatabaseRowAbstract or KDatabaseRowsetAbstract
     * @see KDatabaseRow::save or KDatabaseRowset::save
     */
    public function save()
    {
        $this->getTable()->getCommandChain()->disable();
        $this->getMixer()->save();
        $this->getTable()->getCommandChain()->enable();

        return $this->getMixer();
    }

    /**
     * Deletes the row form the database.
     *
     * This function specialises the KDatabaseRow or KDatabaseRowset delete function and auto-disables the tables command
     * chain to prevent recursive looping.
     *
     * @return KDatabaseRowAbstract
     */
    public function delete()
    {
        $this->getTable()->getCommandChain()->disable();
        $this->getMixer()->delete();
        $this->getTable()->getCommandChain()->enable();

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
