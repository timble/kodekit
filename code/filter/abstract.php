<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Filter
 *
 * If the filter implements KFilterTraversable it will be decorated with KFilterIterator to allow iterating over the data
 * being filtered in case of an array of a Traversable object. If a filter does not implement KFilterTraversable the data
 * will be passed directly to the filter.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
abstract class KFilterAbstract extends KObject implements KFilterInterface, KObjectInstantiable
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
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
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
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Create filter and decorate it with KFilterIterator if the filter implements KFilterTraversable
     *
     * @param   KObjectConfigInterface  $config    Configuration options
     * @param   KObjectManagerInterface $manager A KObjectManagerInterface object
     * @return KFilterInterface
     * @see KFilterTraversable
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        //Create the singleton
        $class    = $manager->getClass($config->object_identifier);
        $instance = new $class($config);

        if($instance instanceof KFilterTraversable) {
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
     * Get a list of error that occurred during sanitize or validate
     *
     * @return array
     */
    public function getErrors()
    {
        return (array) $this->_errors;
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
