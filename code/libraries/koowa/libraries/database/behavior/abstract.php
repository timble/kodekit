<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
abstract class KDatabaseBehaviorAbstract extends KBehaviorDynamic implements KObjectInstantiable
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

        //If the behavior is auto mixed also lazy mix it into related row objects.
        if ($config->auto_mixin)
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
     * @param KCommandInterface $command    The command
     * @param  mixed            $condition  The break condition
     * @return array|mixed Returns an array of the callback results in FIFO order. If a handler breaks and the break
     *                     condition is not NULL returns the break condition.
     */
    public function executeCommand(KCommandInterface $command, $condition = null)
    {
        if ($command->data instanceof KDatabaseRowInterface) {
            $this->setMixer($command->data);
        }

        return parent::executeCommand($command, $condition);
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
     * This function also dynamically adds a function of format is[Behavior] to allow client code to check if the behavior
     * is callable.
     *
     * @param KObjectMixable $mixer  The mixer requesting the mixable methods.
     * @return array  An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null)
    {
        $methods = parent::getMixableMethods($mixer);
        return array_diff($methods, array('save', 'delete', 'getInstance'));
    }
}
