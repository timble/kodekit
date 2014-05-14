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
     * The column name
     *
     * @var string
     */
    protected $_column;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct( KObjectConfig $config = null)
    {
        parent::__construct($config);

        $this->_column = $config->column;
    }

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
            'column'    => 'parameters'
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
        if($this->hasProperty($this->_column) && !isset($this->_parameters))
        {
            $type   = (array) $this->getTable()->getColumn($this->_column)->filter;
            $data   = $this->getProperty($this->_column);
            $config = $this->getObject('object.config.factory')->createFormat($type[0]);

            if(!empty($data))
            {
                if (is_string($data)) {
                    $config->fromString(trim($data));
                } else {
                    $config->append($data);
                }
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

        if($table->hasColumn($this->_column))  {
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
            $context->data->setProperty($this->_column, $context->data->getParameters()->toString());
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
            $context->data->setProperty($this->_column, $context->data->getParameters()->toString());
        }
    }

    /**
     * Get the methods that are available for mixin based
     *
     * @param  array $exclude   A list of methods to exclude
     * @return array  An array of methods
     */
    public function getMixableMethods($exclude = array())
    {
        if($this->_column !== 'parameters')
        {
            $exclude += array('getParameters');
            $methods = parent::getMixableMethods($exclude);

            //Add dynamic methods based on the column name
            $methods['get'.ucfirst($this->_column)] = $this;
            $methods['setProperty'.ucfirst($this->_column)] = $this;
        }
        else $methods = parent::getMixableMethods();

        return $methods;
    }

    /**
     * Intercept parameter getter and setter calls
     *
     * @param  string   $method     The function name
     * @param  array    $arguments  The function arguments
     * @throws BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        if($this->_column !== 'parameters')
        {
            //Call getParameters()
            if($method == 'get'.ucfirst($this->_column)) {
                return $this->getParameters();
            }

            //Call setPropertyParameters()
            if($method == 'setProperty'.ucfirst($this->_column)) {
                return $this->setPropertyParameters($arguments[0]);
            }
        }

        return parent::__call($method, $arguments);
    }
}