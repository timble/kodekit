<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Parameterizable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KDatabaseBehaviorParameterizable extends KDatabaseBehaviorAbstract
{
    /**
     * The parameters
     *
     * @var KObjectConfigInterface
     */
    protected $_parameters;

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'row_mixin' => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the parameters
     *
     * Requires an 'parameters' table column
     *
     * @return KObjectConfigInterface
     */
    public function getParameters()
    {
        if($this->hasProperty('parameters') && !isset($this->_parameters))
        {
            $type = $this->getTable()->getColumn('parameters')->filter->getIdentifier()->name;
            $data = trim($this->getProperty('parameters'));

            //Create the parameters object
            if(empty($data)) {
                $config = $this->getObject('object.config.factory')->createFormat($type);
            } else {
                $config = $this->getObject('bject.config.factory')->fromString($type, $data);
            }

            $this->_parameters = $config;
        }

        return $this->_parameters;
    }

    /**
     * Merge the parameters
     *
     * @param $value
     */
    public function setPropertyParameters($value)
    {
        if(!empty($value))
        {
            if(!is_string($value)) {
                $value = $this->getParameters()->add($value)->toString();
            }
        }

        return $value;
    }

    /**
     * Check if the behavior is supported
     *
     * Behavior requires a 'parameters' table column
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer = $this->getMixer();
        $table = $mixer instanceof KDatabaseRowInterface ?  $mixer->getTable() : $mixer;

        if($table->hasColumn('parameters'))  {
            return true;
        }

        return false;
    }

    /**
     * Insert the parameters
     *
     * @param KDatabaseContext	$context A database context object
     * @return void
     */
    protected function _beforeInsert(KDatabaseContext $context)
    {
        if($context->data->getParameters() instanceof KObjectConfigInterface) {
            $context->data->setProperty('parameters', $context->data->getParameters()->toString());
        }
    }

    /**
     * Update the parameters
     *
     * @param KDatabaseContext	$context A database context object
     * @return void
     */
    protected function _beforeUpdate(KDatabaseContext $context)
    {
        if($context->data->getParameters() instanceof KObjectConfigInterface) {
            $context->data->setProperty('parameters', $context->data->getParameters()->toString());
        }
    }
}