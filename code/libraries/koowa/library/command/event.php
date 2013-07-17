<?php
/**
 * @version		$Id$
 * @package		Koowa_Command
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Event Command
 *
 * The event commend will translate the command name to a onCommandName format
 * and let the event dispatcher dispatch to any registered event handlers.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Command
 * @uses        KService
 * @uses        KEventDispatcher
 * @uses        KInflector
 */
class KCommandEvent extends KCommand
{
    /**
     * The event dispatcher object
     *
     * @var KEventDispatcher
     */
    protected $_dispatcher;

    /**
     * Constructor.
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct( KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();

        parent::__construct($config);

        $this->_dispatcher = $config->dispatcher;
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
            'dispatcher'   => $this->getService('koowa:event.dispatcher')
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Always returns true
     */
    public function execute($name, KCommandContext $context)
    {
        $type = '';
        $package = '';
        $subject = '';

        if ($context->caller)
        {
            $identifier = clone $context->caller->getIdentifier();
            $package = $identifier->package;

            if ($identifier->path)
            {
                $type = array_shift($identifier->path);
                $subject = $identifier->name;
            }
            else {
                $type = $identifier->name;
            }
        }

        $parts  = explode('.', $name);
        $when   = array_shift($parts); // before or after
        $name   = KInflector::implode($parts); // Read Dispatch Select etc.

        // Compile specific & generic event names
        $event_specific = 'on'.ucfirst($when).ucfirst($package).ucfirst($subject).ucfirst($type).$name;
        $event_generic  = 'on'.ucfirst($when).ucfirst($type).$name;

        // Create event object to check for propagation
        $event = new KEvent($event_specific, $context);
        $this->_dispatcher->dispatchEvent($event_specific, $event);

        // Ensure event can be propagated and event name is different
        if ($event->canPropagate() && $event_specific != $event_generic) {
            $this->_dispatcher->dispatchEvent($event_generic, $event);
        }

        return true;
    }
}