<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Command Invoker
 *
 * The event commend will translate the command name to a onCommandName format and let the event dispatcher dispatch
 * to any registered event handlers.
 *
 * The 'clone_context' config option defines if the context is clone before being passed to the event dispatcher or
 * it passed by reference instead. By default the context is cloned.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
class KCommandInvokerEvent extends KEventMixin implements KCommandInvokerInterface
{
    /**
     * The command priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * @var boolean
     */
    protected $_clone_context;

    /**
     * Object constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the command priority
        $this->_priority = $config->priority;

        $this->_clone_context = $config->clone_context;
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
            'clone_context' => true
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * This functions returns void to prevent is from breaking the chain.
     *
     * @param   string  $name    The command name
     * @param   KCommandInterface $context The command context
     * @return  void
     */
    public function execute($name, KCommandInterface $context)
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
        $when   = array_shift($parts);               // Before or After
        $name   = KStringInflector::implode($parts); // Read Dispatch Select etc.

        // Create Specific and Generic event names
        $event_specific = 'on'.ucfirst($when).ucfirst($package).ucfirst($subject).ucfirst($type).$name;
        $event_generic  = 'on'.ucfirst($when).ucfirst($type).$name;

        // Clone the context
        if($this->_clone_context) {
            $event = clone($context);
        } else {
            $event = $context;
        }

        // Create event object to check for propagation
        $event = new KEvent($event_specific, $context);
        $event->setTarget($context->getSubject());

        $this->getEventDispatcher()->dispatch($event_specific, $event);

        // Ensure event can be propagated and event name is different
        if ($event->canPropagate() && $event_specific != $event_generic) {
            $this->getEventDispatcher()->dispatch($event_generic, $event);
        }
    }

    /**
     * Get the methods that are available for mixin.
     *
     * @param  KObjectMixable $mixer Mixer object
     * @return array An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null)
    {
        $methods = parent::getMixableMethods();

        unset($methods['execute']);
        unset($methods['getPriority']);

        return $methods;
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