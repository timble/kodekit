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
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Command
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
     * Object constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the command priority
        $this->_priority = $config->priority;
    }

    /**
     * Command handler
     *
     * This functions returns void to prevent is from breaking the chain.
     *
     * @param   string  $name    The command name
     * @param   KCommandContext $context The command context
     * @return  void
     */
    public function execute($name, KCommandContext $context)
    {
        $type = '';

        if ($context->getSubject())
        {
            $identifier = clone $context->getSubject()->getIdentifier();

            if ($identifier->path) {
                $type = array_shift($identifier->path);
            } else {
                $type = $identifier->name;
            }
        }

        $parts = explode('.', $name);
        $name = 'on' . ucfirst(array_shift($parts)) . ucfirst($type) . KStringInflector::implode($parts);

        if($this->getConfig()->clone_context) {
            $event = clone($context);
        } else {
            $event = $context;
        }

        $event = new KEvent($event);
        $event->setTarget($context->getSubject());

        $this->getEventDispatcher()->dispatchEvent($name, $event);
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