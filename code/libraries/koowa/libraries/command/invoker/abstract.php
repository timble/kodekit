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
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
abstract class KCommandInvokerAbstract extends KObject implements KCommandInvokerInterface
{
    /**
     * Array of command handlers
     *
     * $var array
     */
    private $__command_handlers = array();

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
     * @param KCommandInterface $command    The command
     * @param  mixed            $condition  The break condition
     * @return array|mixed Returns an array of the handler results in FIFO order. If a handler breaks and the break
     *                     condition is not NULL returns the break condition.
     */
    public function executeCommand(KCommandInterface $command, $condition = null)
    {
        $result = array();

        if(isset($this->__command_handlers[$command->getName()]))
        {
            foreach($this->__command_handlers[$command->getName()] as $handler)
            {
                $method = $handler['method'];
                $params = $handler['params'];

                try
                {
                    if(class_exists('Closure') && $method instanceof Closure) {
                        $result[] = $method($command->append($params));
                    } else {
                        $result[$method] = $this->$method($command->append($params));
                    }
                }
                catch (KCommandExceptionHandler $e) {
                    $result[] = $e;
                }

                if($condition !== null && current($result) === $condition)
                {
                    $result = current($result);
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Add a command handler
     *
     * If the handler has already been added. It will not be re-added but parameters will be merged. This allows to
     * change or add parameters for existing handlers.
     *
     * @param  	string          $command  The command name to register the handler for
     * @param 	string|Closure  $method   The name of the method or a Closure object
     * @param   array|object    $params   An associative array of config parameters or a KObjectConfig object
     * @throws  InvalidArgumentException If the callback is not a callable
     * @return  KCommandInvokerAbstract
     */
    public function addCommandHandler($command, $method, $params = array())
    {
        if (is_string($method) && !method_exists($this, $method))
        {
            throw new InvalidArgumentException(
                'Method does not exist '.__CLASS__.'::'.$method
            );
        }

        $params  = (array) KObjectConfig::unbox($params);
        $command = strtolower($command);

        if (!isset($this->__command_handlers[$command]) ) {
            $this->__command_handlers[$command] = array();
        }

        if(class_exists('Closure') && $method instanceof Closure) {
            $index = spl_object_hash($method);
        } else {
            $index = $method;
        }

        if(!isset($this->__command_handlers[$command][$index]))
        {
            $this->__command_handlers[$command][$index]['method'] = $method;
            $this->__command_handlers[$command][$index]['params'] = $params;
        }
        else  $this->__command_handlers[$command][$index]['params'] = array_merge($this->__command_handlers[$command][$index]['params'], $params);

        return $this;
    }

    /**
     * Remove a command handler
     *
     * @param  	string	        $command  The command to unregister the handler from
     * @param 	string|Closure	$method   The name of the method or a Closure object to unregister
     * @return  KCommandInvokerAbstract
     */
    public function removeCommandHandler($command, $method)
    {
        $command = strtolower($command);

        if (isset($this->__command_handlers[$command]) )
        {
            if(class_exists('Closure') && $method instanceof Closure) {
                $index = spl_object_hash($method);
            } else {
                $index = $method;
            }

            unset($this->__command_handlers[$command][$index]);
        }

        return $this;
    }

    /**
     * Get the handlers for a command
     *
     * @param string $command   The command
     * @return  array An array of command handlers
     */
    public function getCommandHandlers($command)
    {
        $result = array();
        if (isset($this->__command_handlers[$command]) ) {
            $result = array_values($this->__command_handlers[$command]);
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