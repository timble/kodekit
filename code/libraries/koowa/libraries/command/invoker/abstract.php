<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Invoker
 *
 * The command invoker will translate the command name to a method name, format and call it for the object class to
 * handle it if the method exists.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
abstract class KCommandInvokerAbstract extends KObject implements KCommandInvokerInterface
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
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * @param   string          $name     The command name
     * @param   KCommandInterface  $context  The command context
     *
     * @return  mixed  Method result if the method exists, NULL otherwise.
     */
    public function execute($name, KCommandInterface $context)
    {
        $result = null;


        $type = '';

        if($context->subject)
        {
            $identifier = clone $context->subject->getIdentifier();

            if($identifier->path) {
                $type = array_shift($identifier->path);
            } else {
                $type = $identifier->name;
            }
        }

        $parts  = KStringInflector::implode(explode('.', $name));
        $method = empty($type) ? '_'.lcfirst($parts) : '_'.$type.ucfirst($parts);

        //If the method exists call the method and return the result
        if(in_array($method, $this->getMethods())) {
            $result = $this->$method($context);
        }

        return $result;
    }

    /**
     * Get the priority of the command
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }
}