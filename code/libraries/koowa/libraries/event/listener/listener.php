<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Listener
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
class KEventListener extends KObject implements KEventListenerInterface
{
 	/**
     * List of event handlers
     *
     * @var array
     */
    private $__event_handlers;

    /**
     * The event priority
     *
     * @var int
     */
    protected $_priority;

	/**
	 * Constructor.
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		if($config->auto_connect)
		{
		    if(!($config->dispatcher instanceof KEventDispatcher)) {
		        $config->dispatcher = $this->getObject($config->dispatcher);
		    }

		    $this->connect($config->dispatcher);
		}
	}

 	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
        	'dispatcher'   => 'koowa:event.dispatcher',
    	    'auto_connect' => true,
    		'priority'     => KEventInterface::PRIORITY_NORMAL
        ));

        parent::_initialize($config);
    }

    /**
     * Get the event handlers of the listener
     *
     * Event handlers always start with 'on' and need to be public methods
     *
     * @return array An array of public methods
     */
    public function getEventHandlers()
    {
        if(!$this->__event_handlers)
        {
            $handlers  = array();

            //Get all the public methods
            $reflection = new ReflectionClass($this);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
            {
                if(substr($method->name, 0, 2) == 'on') {
                    $handlers[] = $method->name;
                }
            }

            $this->__event_handlers = $handlers;
        }

        return $this->__event_handlers;
    }

    /**
     * Connect to an event dispatcher
     *
     * @param  KEventDispatcher $dispatcher	The event dispatcher to connect too
     * @return KEventListener
     */
    public function connect(KEventDispatcher $dispatcher)
    {
        $handlers = $this->getEventHandlers();

        foreach($handlers as $handler) {
            $dispatcher->addEventListener($handler, $this, $this->_priority);
        }

        return $this;
    }

	/**
     * Disconnect from an event dispatcher
     *
     * @param  KEventDispatcher $dispatcher	The event dispatcher to disconnect from
     * @return KEventListener
     */
    public function disconnect(KEventDispatcher $dispatcher)
    {
        $handlers = $this->getEventHandlers();

        foreach($handlers as $handler) {
            $dispatcher->removeEventListener($handler, $this);
        }

        return $this;
    }
}
