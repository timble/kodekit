<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Command
 *
 * The event commend will translate the command name to a onCommandName format and let the event dispatcher dispatch to
 * any registered event handlers.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
class KCommandEvent extends KObjectMixinAbstract implements KCommandInvokerInterface
{
    /**
     * Event dispatcher object
     *
     * @var KEventDispatcherInterface
     */
    protected $_event_dispatcher;

    /**
     * The command priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Object constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (is_null($config->event_dispatcher)) {
            throw new InvalidArgumentException('event_dispatcher [KEventDispatcherInterface] config option is required');
        }

        //Set the event dispatcher
        $this->_event_dispatcher = $config->event_dispatcher;

        //Set the command priority
        $this->_priority = $config->priority;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'event_dispatcher'  => 'koowa:event.dispatcher',
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * @param   string          $name     The command name
     * @param   KCommandContext $context  The command context
     * @return  boolean Always returns TRUE
     */
    public function execute($name, KCommandContext $context)
    {
        $type    = '';
        $package = '';
        $subject = '';

        if ($context->getSubject())
        {
            $identifier = clone $context->getSubject()->getIdentifier();
            $package = $identifier->package;

            if ($identifier->path)
            {
                $type = array_shift($identifier->path);
                $subject = $identifier->name;
            }
            else $type = $identifier->name;
        }

        $parts  = explode('.', $name);
        $when   = array_shift($parts);         // Before or After
        $name   = KStringInflector::implode($parts); // Read Dispatch Select etc.

        // Create Specific and Generic event names
        $event_specific = 'on'.ucfirst($when).ucfirst($package).ucfirst($subject).ucfirst($type).$name;
        $event_generic  = 'on'.ucfirst($when).ucfirst($type).$name;

        // Create event object to check for propagation
        $event = new KEvent($event_specific, $context);
        $event->setTarget($context->getSubject());

        $this->getEventDispatcher()->dispatchEvent($event_specific, $event);

        // Ensure event can be propagated and event name is different
        if ($event->canPropagate() && $event_specific != $event_generic) {
            $this->getEventDispatcher()->dispatchEvent($event_generic, $event);
        }

        return true;
    }

    /**
     * Get the event dispatcher
     *
     * @return  KEventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if(!$this->_event_dispatcher instanceof KEventDispatcherInterface)
        {
            $this->_event_dispatcher = $this->getObject($this->_event_dispatcher);

            //Make sure the request implements ControllerRequestInterface
            if(!$this->_event_dispatcher instanceof KEventDispatcherInterface)
            {
                throw new UnexpectedValueException(
                    'EventDispatcher: '.get_class($this->_event_dispatcher).' does not implement KEventDispatcherInterface'
                );
            }
        }

        return $this->_event_dispatcher;
    }

    /**
     * Set the event dispatcher
     *
     * @param   KEventDispatcherInterface  $dispatcher An event dispatcher object
     * @return  KObject  The mixer object
     */
    public function setEventDispatcher(KEventDispatcherInterface $dispatcher)
    {
        $this->_event_dispatcher = $dispatcher;
        return $this->getMixer();
    }

    /**
     * Get the methods that are available for mixin.
     *
     * @param  Object $mixer Mixer object
     * @return array An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null)
    {
        $methods = parent::getMixableMethods();

        return array_diff($methods, array('execute', 'getPriority'));
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
}
