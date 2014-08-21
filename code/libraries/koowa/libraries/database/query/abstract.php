<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Database Query
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Query
 */
abstract class KDatabaseQueryAbstract extends KObject implements KDatabaseQueryInterface
{
    /**
     * Database adapter
     *
     * @var     object
     */
    protected $_adapter;

    /**
     * Query parameters to bind
     *
     * @var array
     */
    protected $_parameters;

    /**
     * Constructor
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_adapter = $config->adapter;
        $this->setParameters(KObjectConfig::unbox($config->parameters));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'adapter'    => 'lib:database.adapter.mysqli',
            'parameters' => array()
        ));
    }

    /**
     * Bind values to a corresponding named placeholders in the query.
     *
     * @param  array $params Associative array of parameters.
     * @return \KDatabaseQueryInterface
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
     * @return  KDatabaseQueryParameters
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Gets the database adapter
     *
     * @throws	\UnexpectedValueException	If the adapter doesn't implement KDatabaseAdapterInterface
     * @return KDatabaseAdapterInterface
     */
    public function getAdapter()
    {
        if(!$this->_adapter instanceof KDatabaseAdapterInterface)
        {
            $this->_adapter = $this->getObject($this->_adapter);

            if(!$this->_adapter instanceof KDatabaseAdapterInterface)
            {
                throw new UnexpectedValueException(
                    'Adapter: '.get_class($this->_adapter).' does not implement KDatabaseAdapterInterface'
                );
            }
        }

        return $this->_adapter;
    }

    /**
     * Set the database adapter
     *
     * @param KDatabaseAdapterInterface $adapter
     * @return KDatabaseQueryInterface
     */
    public function setAdapter(KDatabaseAdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
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

        if(!$value instanceof KDatabaseQuerySelect) {
            $value = is_object($value) ? (string) $value : $value;
            $replacement = $this->getAdapter()->quoteValue($value);
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
        if($name = 'params') {
            return $this->getParameters();
        }

        return parent::__get($name);
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
