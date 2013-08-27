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
abstract class KDatabaseBehaviorAbstract extends KObjectMixinAbstract implements KDatabaseBehaviorInterface
{
	/**
	 * The behavior priority
	 *
	 * @var integer
	 */
	protected $_priority;

	/**
     * The object identifier
     *
     * @var KObjectIdentifier
     */
    private $__object_identifier;

    /**
     * The object manager
     *
     * @var KObjectManagerInterface
     */
    private $__object_manager;

	/**
	 * Constructor.
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct( KObjectConfig $config = null)
	{
	    //Set the object manager
        if(isset($config->object_manager)) {
            $this->__object_manager = $config->object_manager;
        }

        //Set the object identifier
        if(isset($config->object_identifier)) {
            $this->__object_identifier = $config->object_identifier;
        }

		parent::__construct($config);

		$this->_priority = $config->priority;

	    //Automatically mixin the behavior with the mixer (table object)
		if($config->auto_mixin) {
		    $this->mixin($this);
		}
	}

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
	protected function _initialize(KObjectConfig $config)
    {
    	$config->append(array(
			'priority'   => KCommand::PRIORITY_NORMAL,
    	    'auto_mixin' => false
	  	));

    	parent::_initialize($config);
   	}

	/**
	 * Get the priority of a behavior
	 *
	 * @return	integer The command priority
	 */
  	public function getPriority()
  	{
  		return $this->_priority;
  	}

	/**
	 * Command handler
	 *
	 * This function translated the command name to a command handler function of the format '_beforeX[Command]' or
     * '_afterX[Command]. Command handler functions should be declared protected.
	 *
	 * @param 	string  	    $name    The command name
	 * @param 	KCommandContext $context The command context
	 * @return 	boolean		Can return both true or false.
	 */
	final public function execute($name, KCommandContext $context)
	{
		$identifier = clone $context->caller->getIdentifier();
		$type       = array_pop($identifier->path);

		$parts  = explode('.', $name);
		$method = '_'.$parts[0].ucfirst($type).ucfirst($parts[1]);

		if(method_exists($this, $method))
		{
			if($context->data instanceof KDatabaseRowInterface) {
			     $this->setMixer($context->data);
			}

			return $this->$method($context);
		}

		return true;
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
     * Get an object handle
     *
     * This function only returns a valid handle if one or more command handler functions are defined. A commend handler
     * function needs to follow the following format : '_afterX[Event]' or '_beforeX[Event]' to be recognised.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        $methods = $this->getMethods();

        foreach($methods as $method)
        {
            if(substr($method, 0, 7) == '_before' || substr($method, 0, 6) == '_after') {
                return parent::getHandle();
            }
        }

        return null;
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
        $methods   = parent::getMixableMethods($mixer);
        $methods[] = 'is'.ucfirst($this->getIdentifier()->name);

        return array_diff($methods, array('execute', 'save', 'delete', 'getHandle', 'getPriority', 'getIdentifier', 'getObject'));
    }

	/**
	 * Get an instance of a class based on a class identifier only creating it if it doesn't exist yet.
	 *
	 * @param	string|object	$identifier The class identifier or identifier object
	 * @param	array  			$config     An optional associative array of configuration settings.
	 * @return	object  		Return object on success, throws exception on failure
	 * @see 	KObjectInterface
	 */
	final public function getObject($identifier, array $config = array())
	{
	    return $this->__object_manager->getObject($identifier, $config);
	}

	/**
	 * Gets the object identifier.
	 *
     * @param	string|object	$identifier The class identifier or identifier object
	 * @return	KObjectIdentifier
	 * @see 	KObjectInterface
	 */
	final public function getIdentifier($identifier = null)
	{
		if(isset($identifier)) {
		    $result = $this->__object_manager->getIdentifier($identifier);
		} else {
		    $result = $this->__object_identifier;
		}

	    return $result;
	}
}
