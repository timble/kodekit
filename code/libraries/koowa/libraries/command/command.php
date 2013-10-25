<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command
 *
 * The command handler will translate the command name into a function format and call it for the object class to
 * handle it if the method exists.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
class KCommand extends KObject implements KCommandInterface
{
    /**
     * The command priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct( KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_priority = $config->priority;
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
            'priority'   => self::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * @param   string          $name     The command name
     * @param   KCommandContext $context  The command context
     * @return  boolean         Can return both true or false.
     */
    public function execute( $name, KCommandContext $context)
    {
        $type = '';

        if($context->caller)
        {
            $identifier = clone $context->caller->getIdentifier();

            if($identifier->path) {
                $type = array_shift($identifier->path);
            } else {
                $type = $identifier->name;
            }
        }

        $parts  = explode('.', $name);
        $method = !empty($type) ? '_'.$type.ucfirst(KStringInflector::implode($parts)) : '_'.lcfirst(KStringInflector::implode($parts));

        if(in_array($method, $this->getMethods())) {
            return $this->$method($context);
        }

        return true;
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
