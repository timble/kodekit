<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Model
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model
 */
abstract class KModelAbstract extends KObject implements KModelInterface
{
    /**
     * A state object
     *
     * @var KModelStateInterface
     */
    private $__state;

	/**
	 * List total
	 *
	 * @var integer
	 */
	protected $_total;

	/**
	 * Model list data
	 *
	 * @var array
	 */
	protected $_list;

	/**
	 * Model item data
	 *
	 * @var mixed
	 */
	protected $_item;

	/**
	 * Constructor
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config = null)
	{
		parent::__construct($config);

        // Set the state identifier
        $this->__state = $config->state;
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   KObjectConfig $config Configuration options
	 * @return  void
	 */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'state' => 'koowa:model.state',
        ));

        parent::_initialize($config);
    }

	/**
	 * Test the connected status of the model.
	 *
	 * @return	boolean	Returns TRUE by default.
	 */
    public function isConnected()
	{
	    return true;
	}

    /**
     * Reset all cached data and reset the model state to it's default
     *
     * @param   boolean $default If TRUE use defaults when resetting. Default is TRUE
     * @return KModelAbstract
     */
    public function reset($default = true)
    {
        $this->_list  = null;
        $this->_item  = null;
        $this->_total = null;

        $this->getState()->reset($default);

        return $this;
    }

    /**
     * Set the model state values
     *
     * @param  array $values Set the state values
     * @return $this
     */
    public function setState(array $values)
    {
        $this->getState()->setValues($values);
        return $this;
    }

    /**
     * Get the model state object
     *
     * @throws UnexpectedValueException
     * @return  KModelStateInterface  The model state object
     */
    public function getState()
    {
        if(!$this->__state instanceof KModelStateInterface)
        {
            $this->__state = $this->getObject($this->__state, array('model' => $this));

            if(!$this->__state instanceof KModelStateInterface)
            {
                throw new UnexpectedValueException(
                    'State: '.get_class($this->__state).' does not implement ModelStateInterface'
                );
            }
        }

        return $this->__state;
    }

    /**
     * State Change notifier
     *
     * This function is called when the state has changed.
     *
     * @param  string 	$name  The state name being changed
     * @return void
     */
    public function onStateChange($name)
    {
        $this->_rowset = null;
        $this->_row    = null;
        $this->_total  = null;
    }

    /**
     * Method to get a item
     *
     * @return  KDatabaseRowInterface
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get a list of items
     *
     * @return  KDatabaseRowsetInterface
     */
    public function getList()
    {
        return $this->_list;
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal()
    {
        return $this->_total;
    }

	/**
     * Get the model data
     *
     * If the model state is unique this function will call getItem(), otherwise
     * it will call getList().
     *
     * @return KDatabaseRowsetInterface|KDatabaseRowInterface
     */
    public function getData()
    {
        if($this->getState()->isUnique()) {
            $data = $this->getItem();
        } else {
            $data = $this->getList();
        }

        return $data;
    }

    /**
     * Supports a simple form Fluent Interfaces. Allows you to set states by using the state name as the method name.
     *
     * For example : $model->sort('name')->limit(10)->getRowset();
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return  $this
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        if ($this->getState()->has($method))
        {
            $this->getState()->set($method, $args[0]);
            return $this;
        }

        return parent::__call($method, $args);
    }

    /**
     * Preform a deep clone of the object.
     *
     * @retun void
     */
    public function __clone()
    {
        parent::__clone();

        $this->__state = clone $this->__state;
    }
}
