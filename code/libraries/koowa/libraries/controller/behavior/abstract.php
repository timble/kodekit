<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */
/**
 * Abstract Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerBehaviorAbstract extends KMixinAbstract implements KControllerBehaviorInterface
{
	/**
	 * The behavior priority
	 *
	 * @var integer
	 */
	protected $_priority;

	/**
     * The service identifier
     *
     * @var KServiceIdentifier
     */
    private $__service_identifier;

    /**
     * The service container
     *
     * @var KServiceInterface
     */
    private $__service_container;

	/**
	 * Constructor.
	 *
	 * @param   KConfig $config Configuration options
	 */
	public function __construct( KConfig $config = null)
	{
	    //Set the service container
        if(isset($config->service_container)) {
            $this->__service_container = $config->service_container;
        }

        //Set the service identifier
        if(isset($config->service_identifier)) {
            $this->__service_identifier = $config->service_identifier;
        }

	    parent::__construct($config);

		$this->_priority = $config->priority;

		//Automatically mixin the behavior
		if($config->auto_mixin) {
		    $this->mixin($this);
		}
	}

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return void
     */
	protected function _initialize(KConfig $config)
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
	 * This function translates the command name to a command handler function of the format '_before[Command]' or
     * '_after[Command]. Command handler functions should be declared protected.
	 *
	 * @param 	string           $name	    The command name
	 * @param 	KCommandContext  $context 	The command context
	 * @return 	boolean
	 */
	public function execute($name, KCommandContext $context)
	{
		$identifier = clone $context->caller->getIdentifier();
		$type       = array_pop($identifier->path);

		$parts  = explode('.', $name);
		$method = '_'.$parts[0].ucfirst($parts[1]);

		if(method_exists($this, $method))
		{
			$this->setMixer($context->caller);
			return $this->$method($context);
		}

		return true;
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
     * @param KObject $mixer The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods   = parent::getMixableMethods($mixer);
        $methods[] = 'is'.ucfirst($this->getIdentifier()->name);

        foreach($this->getMethods() as $method)
        {
            if(substr($method, 0, 7) == '_action') {
                $methods[] = strtolower(substr($method, 7));
            }
        }

        return array_diff($methods, array('execute', 'getIdentifier', 'getPriority', 'getHandle', 'getService', 'getIdentifier'));
    }

	/**
	 * Get an instance of a class based on a class identifier only creating it if it doesn't exist yet.
	 *
	 * @param	string|object	$identifier The class identifier or identifier object
	 * @param	array  			$config     An optional associative array of configuration settings.
	 * @return	object  		Return object on success, throws exception on failure
	 * @see 	KObjectServiceable
	 */
	final public function getService($identifier, array $config = array())
	{
	    return $this->__service_container->get($identifier, $config);
	}

    /**
     * Gets the service identifier.
     *
     * @param mixed $identifier
     * @return KServiceIdentifier
     * @see    KObjectServiceable
     */
	final public function getIdentifier($identifier = null)
	{
		if(isset($identifier)) {
		    $result = $this->__service_container->getIdentifier($identifier);
		} else {
		    $result = $this->__service_identifier;
		}

	    return $result;
	}
}
