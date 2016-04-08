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
 * Abstract Filter
 *
 * If the filter implements FilterTraversable it will be decorated with FilterIterator to allow iterating over the data
 * being filtered in case of an array of a Traversable object. If a filter does not implement FilterTraversable the data
 * will be passed directly to the filter.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Filter
 */
abstract class FilterAbstract extends Object implements FilterInterface, ObjectInstantiable
{
    /**
     * The filter errors
     *
     * @var	array
     */
    protected $_errors = array();

    /**
     * The filter priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_priority = $config->priority;

        foreach($config as $key => $value)
        {
            if(property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Create filter and decorate it with FilterIterator if the filter implements FilterTraversable
     *
     * @param   ObjectConfigInterface  $config    Configuration options
     * @param   ObjectManagerInterface $manager A ObjectManagerInterface object
     * @return FilterInterface
     * @see FilterTraversable
     */
    public static function getInstance(ObjectConfigInterface $config, ObjectManagerInterface $manager)
    {
        //Create the singleton
        $class    = $manager->getClass($config->object_identifier);
        $instance = new $class($config);

        if($instance instanceof FilterTraversable) {
            $instance = $instance->decorate('lib:filter.iterator');
        }

        return $instance;
    }

    /**
     * Validate a scalar or traversable value
     *
     * NOTE: This should always be a simple yes/no question (is $value valid?), so only true or false should be returned
     *
     * @param   mixed   $value Value to be validated
     * @return  bool    True when the value is valid. False otherwise.
     */
    public function validate($value)
    {
        return false;
    }

    /**
     * Sanitize a scalar or traversable value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  mixed   The sanitized value
     */
    public function sanitize($value)
    {
        return $value;
    }

    /**
     * Resets any generated errors for the filter
     *
     * @return FilterAbstract
     */
    public function reset()
    {
        $this->_errors = array();
        return $this;
    }

    /**
     * Get a list of error that occurred during sanitize or validate
     *
     * @return array
     */
    public function getErrors()
    {
        return (array) $this->_errors;
    }

    /**
     * Add an error message
     *
     * @param string $message The error message
     * @return FilterAbstract
     */
    public function addError($message)
    {
        $this->_errors[] = $message;
        return $this;
    }

    /**
     * Get the priority of the filter
     *
     * @return  integer The priority level
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Add an error message
     *
     * @param $message
     * @return boolean Returns false
     */
    protected function _error($message)
    {
        $this->_errors[] = $message;
        return false;
    }
}
