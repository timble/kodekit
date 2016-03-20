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
 * Abstract Database Query
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Database\Query
 */
abstract class DatabaseQueryAbstract extends Object implements DatabaseQueryInterface
{
    /**
     * Query parameters to bind
     *
     * @var array
     */
    protected $_parameters;

    /**
     * Database driver
     *
     * @var  DatabaseDriverInterface
     */
    private $__driver;

    /**
     * Constructor
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->__driver = $config->driver;
        $this->setParameters(ObjectConfig::unbox($config->parameters));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'driver'     => 'lib:database.driver.mysqli',
            'parameters' => array()
        ));
    }

    /**
     * Bind values to a corresponding named placeholders in the query.
     *
     * @param  array $params Associative array of parameters.
     * @return DatabaseQueryInterface
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            $this->getParameters()->set($key, $value);
        }

        return $this;
    }

    /**
     * Set the query parameters
     *
     * @param  array $parameters
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = $this->getObject('lib:database.query.parameters', array('parameters' => $parameters));
        return $this;
    }

    /**
     * Get the query parameters
     *
     * @return  DatabaseQueryParameters
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Gets the database driver
     *
     * @throws	\\UnexpectedValueException	If the driver doesn't implement DatabaseDriverInterface
     * @return DatabaseDriverInterface
     */
    public function getDriver()
    {
        if(!$this->__driver instanceof DatabaseDriverInterface)
        {
            $this->__driver = $this->getObject($this->__driver);

            if(!$this->__driver instanceof DatabaseDriverInterface)
            {
                throw new \UnexpectedValueException(
                    'Driver: '.get_class($this->__driver).' does not implement DatabaseDriverInterface'
                );
            }
        }

        return $this->__driver;
    }

    /**
     * Set the database driver
     *
     * @param DatabaseDriverInterface $driver
     * @return DatabaseQueryInterface
     */
    public function setDriver(DatabaseDriverInterface $driver)
    {
        $this->__driver = $driver;
        return $this;
    }

    /**
     * Replace parameters in the query string.
     *
     * @param  string $query The query string.
     * @return string The replaced string.
     */
    protected function _replaceParams($query)
    {
        return preg_replace_callback('/(?<!\w):\w+/', array($this, '_replaceParamsCallback'), $query);
    }

    /**
     * Callback method for parameter replacement.
     *
     * @param  array  $matches Matches of preg_replace_callback.
     * @return string The replaced string.
     */
    protected function _replaceParamsCallback($matches)
    {
        $key   = substr($matches[0], 1);
        $value = $this->_parameters[$key];

        if(!$value instanceof DatabaseQuerySelect) {
            $value = is_object($value) ? (string) $value : $value;
            $replacement = $this->getDriver()->quoteValue($value);
        }
        else $replacement = '('.$value.')';

        return is_array($value) ? '('.$replacement.')' : $replacement;
    }

    /**
     * Get a property
     *
     * Implement a virtual 'params' property to return the params object.
     *
     * @param   string $name  The property name.
     * @return  string $value The property value.
     */
    public function __get($name)
    {
        if ($name == 'params') {
            return $this->getParameters();
        }

        return null;
    }

    /**
     * Render the query to a string.
     *
     * @return  string  The query string.
     */
    final public function __toString()
    {
        return $this->toString();
    }
}
