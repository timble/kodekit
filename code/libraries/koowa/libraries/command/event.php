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
class KCommandEvent extends KCommand
{
    /**
     * The event dispatcher object
     *
     * @var KEventDispatcher
     */
    protected $_event_dispatcher;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct( KObjectConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KObjectConfig();

        parent::__construct($config);

        $this->_event_dispatcher = $config->event_dispatcher;
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
            'event_dispatcher' => $this->getObject('koowa:event.dispatcher')
        ));

        parent::_initialize($config);
    }

    /**
     * Get the event dispatcher
     *
     * @return  KEventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->_event_dispatcher;
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
}
