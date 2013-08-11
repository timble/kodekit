<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Dispatcher Mixin
 *
 * Class can be used as a mixin in classes that want to implement an event dispatcher and allow adding and removing
 * listeners.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Mixin
 */
class KMixinEventdispatcher extends KMixinAbstract
{
    /**
     * Event dispatcher object
     *
     * @var KEventDispatcher
     */
    protected $_event_dispatcher;

    /**
     * Object constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Create a event dispatcher object
        $this->_event_dispatcher = $config->event_dispatcher;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'event_dispatcher' => new KEventDispatcher(),
        ));

        parent::_initialize($config);
    }

	/**
     * Get the event dispatcher
     *
     * @return  KEventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->_event_dispatcher;
    }

    /**
     * Set the chain of command object
     *
     * @param   object 		An event dispatcher object
     * @return  KObject     The mixer object
     */
    public function setEventDispatcher(KEventDispatcher $dispatcher)
    {
        $this->_event_dispatcher = $dispatcher;
        return $this->_mixer;
    }

	/**
     * Add an event listener
     *
     * @param  string  The event name
     * @param  object  An object implementing the KObjectHandlable interface
     * @param  integer The event priority, usually between 1 (high priority) and 5 (lowest),
     *                 default is 3. If no priority is set, the command priority will be used
     *                 instead.
     * @return  KObject The mixer objects
     */
    public function addEventListener($event, KObjectHandlable $listener, $priority = KEvent::PRIORITY_NORMAL)
    {
        $this->_event_dispatcher->addEventListener($event, $listener, $priority);
        return $this->_mixer;
    }

    /**
     * Remove an event listener
     *
     * @param   string  The event name
     * @param   object  An object implementing the KObjectHandlable interface
     * @return  KObject  The mixer object
     */
    public function removeEventListener($event, KObjectHandlable $listener)
    {
        $this->_event_dispatcher->removeEventListener($event, $listener);
        return $this->_mixer;
    }
}
