<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

/**
 * Event Profiler
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Event
 */
class KEventProfiler extends KObjectDecorator implements KEventProfilerInterface
{
   /**
    * The start time
    * 
    * @var int
    */
    protected $_start = 0;

    /**
     * Enabled status of the profiler
     *
     * @var boolean
     */
    protected $_enabled;
    
    /**
     * Array of profile marks
     *
     * @var array
     */
    protected $_profiles;
 	
 	/**
     * Constructor.
     *
     * @param KObjectConfig $config An optional Library\ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {          
        parent::__construct($config);
        
        $this->_start = $config->start;
    }
    
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config  An optional Library\ObjectConfig object with configuration options
     * @return void
	 */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
        	'start'   => microtime(true),
        ));

       parent::_initialize($config);
    }

    /**
     * Enable the profiler
     *
     * @return  KEventProfiler
     */
    public function enable()
    {
        $this->_enabled = true;
        return $this;
    }

    /**
     * Disable the profiler
     *
     * @return  KEventProfiler
     */
    public function disable()
    {
        $this->_enabled = false;
        return $this;
    }

    /**
     * Dispatches an event by dispatching arguments to all listeners that handle the event and returning their return
     * values.
     *
     * This function will add a mark to the profiler for each event dispatched
     *
     * @param   string        $name  The event name
     * @param   object|array  $event An array, a Library\ObjectConfig or a Library\Event object
     * @return  KEventProfiler
     */
    public function dispatchEvent($name, $event = array())
    {
        if($this->isEnabled())
        {
            $this->_profiles[] = array(
                'message' => $name,
                'time'    => $this->getElapsedTime(),
                'memory'  => $this->getMemoryUsage(),
                'target'  => $event->getTarget()->getIdentifier()
            );
        }

        return $this->getDelegate()->dispatchEvent($name, $event);
    }

    /**
     * Add an event listener
     *
     * @param  string            $name The event name
     * @param  KObjectHandlable  $listener An object implementing the KObjectHandlable interface
     * @param  integer           $priority The event priority, usually between 1 (high priority) and 5 (lowest),
     *                                     default is 3. If no priority is set, the command priority will be used
     *                                     instead.
     * @return KEventDispatcher
     */
    public function addEventListener($name, KObjectHandlable $listener, $priority = KEventInterface::PRIORITY_NORMAL)
    {
        $this->getDelegate()->addEventListener($name, $listener, $priority);
        return $this;
    }

    /**
     * Remove an event listener
     *
     * @param   string           $name      The event name
     * @param   KObjectHandlable $listener  An object implementing the KObjectHandlable interface
     * @return  KEventDispatcherInterface
     */
    public function removeEventListener($name, KObjectHandlable $listener)
    {
        $this->getDelegate()->removeEventListener($name, $listener);
        return $this;
    }

    /**
     * Get a list of listeners for a specific event
     *
     * @param   string  $name The event name
     * @return  KObjectQueue An object queue containing the listeners
     */
    public function getListeners($name)
    {
        return $this->getDelegate()->getListeners($name);
    }

    /**
     * Check if we are listening to a specific event
     *
     * @param   string  $name The event name
     * @return  boolean	TRUE if we are listening for a specific event, otherwise FALSE.
     */
    public function hasListeners($name)
    {
        return $this->getDelegate()->hasListeners($name);
    }

    /**
     * Set the priority of an event
     *
     * @param  string            $name     The event name
     * @param  KObjectHandlable  $listener  An object implementing the KObjectHandlable interface
     * @param  integer           $priority The event priority
     * @return KEventDispatcherInterface
     */
    public function setEventPriority($name, KObjectHandlable $listener, $priority)
    {
        $this->getDelegate()->setEventPriority($name, $listener, $priority);
        return $this;
    }

    /**
     * Get the priority of an event
     *
     * @param   string            $name     The event name
     * @param   KObjectHandlable  $listener An object implementing the KObjectHandlable interface
     * @return  integer|boolean The event priority or FALSE if the event isn't listened for.
     */
    public function getEventPriority($name, KObjectHandlable $listener)
    {
        return $this->getDelegate()->getEventPriority($name, $listener);
    }

    /**
     * Get the list of event profiles
     *
     * @return array Array of event profiles
     */
    public function getProfiles()
    {
        return $this->_profiles;
    }
    
	/**
     * Get information about current memory usage.
     *
     * @return int The memory usage
     * @link PHP_MANUAL#memory_get_usage
     */
    public function getMemoryUsage()
    {
        $size = memory_get_usage(true);
        $unit = array('b','kb','mb','gb','tb','pb');
                
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
    
	/**
	 * Gets the total time elapsed for all calls of this timer.
	 *
	 * @return float Time in seconds
	 */
    public function getElapsedTime()
    {
        return microtime(true) - $this->_start;
    }

    /**
     * Check of the command chain is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Set the decorated event dispatcher
     *
     * @param   KEventDispatcherInterface $delegate The decorated event dispatcher
     * @return  KEventProfiler
     * @throws  \InvalidArgumentException If the delegate is not an event dispatcher
     */
    public function setDelegate($delegate)
    {
        if (!$delegate instanceof KEventDispatcherInterface) {
            throw new \InvalidArgumentException('EventDispatcher: '.get_class($delegate).' does not implement KEventDispatcherInterface');
        }

        return parent::setDelegate($delegate);
    }
}