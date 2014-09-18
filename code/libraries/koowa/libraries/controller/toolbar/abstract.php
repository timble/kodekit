<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Controller Toolbar
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar
 */
abstract class KControllerToolbarAbstract extends KCommandHandlerAbstract implements KControllerToolbarInterface
{
    /**
     * Controller object
     *
     * @var     array
     */
    protected $_controller = null;

    /**
     * The commands
     *
     * @var array
     */
    protected $_commands = array();

    /**
     * The toolbar type
     *
     * @var array
     */
    protected $_type;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create the commands array
        $this->_commands = array();

        //Set the toolbar type
        $this->_type = $config->type;

        // Set the controller
        $this->setController($config->controller);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'type'       => 'toolbar',
            'controller' => null,
            'priority'   => self::PRIORITY_HIGH
        ));

        parent::_initialize($config);
    }

    /**
     * Get the toolbar type
     *
     * @return  string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the toolbar's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getIdentifier()->name;
    }

    /**
     * Get the controller object
     *
     * @return  KControllerAbstract
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Set the controller
     *
     * @param   KControllerInterface $controller Controller
     * @return  KControllerToolbarAbstract
     */
    public function setController(KControllerInterface $controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * Add a command
     *
     * @param   string    $command The command name
     * @param   mixed    $config  Parameters to be passed to the command
     * @return  KControllerToolbarCommand  The command that was added
     */
    public function addCommand($command, $config = array())
    {
        if (!($command instanceof  KControllerToolbarCommandInterface)) {
            $command = $this->getCommand($command, $config);
        }

        //Set the command parent
        $command->setParent($command);

        $this->_commands[$command->getName()] = $command;
        return $command;
    }

    /**
     * Get a command by name
     *
     * @param string $name  The command name
     * @param array $config An optional associative array of configuration settings
     * @return KControllerToolbarCommandInterface|boolean A toolbar command if found, false otherwise.
     */
    public function getCommand($name, $config = array())
    {
        if(!isset($this->_commands[$name]))
        {
            //Create the config object
            $command = new KControllerToolbarCommand($name, $config);

            //Attach the command to the toolbar
            $command->setToolbar($this);

            //Find the command function to call
            if (method_exists($this, '_command' . ucfirst($name)))
            {
                $function = '_command' . ucfirst($name);
                $this->$function($command);
            }
            else
            {
                //Don't set an action for GET commands
                if (!isset($command->href))
                {
                    $command->append(array(
                        'attribs' => array(
                            'data-action' => $command->getName()
                        )
                    ));
                }
            }
        }
        else $command = $this->_commands[$name];

        return $command;
    }

    /**
     * Check if a command exists
     *
     * @param string $name  The command name
     * @return boolean True if the command exists, false otherwise.
     */
    public function hasCommand($name)
    {
        return isset($this->_commands[$name]);
    }

    /**
     * Removes a command if exists
     *
     * @param string $name  The command name
     * @return $this
     */
    public function removeCommand($name)
    {
        unset($this->_commands[$name]);

        return $this;
    }

    /**
     * Get the list of commands
     *
     * @return  array
     */
    public function getCommands()
    {
        return $this->_commands;
    }

    /**
     * Get a new iterator
     *
     * @return  RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new RecursiveArrayIterator($this->getCommands());
    }

    /**
     * Reset the commands array
     *
     * @return  KControllerToolbarAbstract
     */
    public function reset()
    {
        unset($this->_commands);
        $this->_commands = array();
        return $this;
    }

    /**
     * Return the command count
     *
     * Required by Countable interface
     *
     * @return  integer
     */
    public function count()
    {
        return count($this->_commands);
    }

    /**
     * Add a command by it's name
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return mixed
     * @see addCommand()
     */
    public function __call($method, $args)
    {
        $parts = KStringInflector::explode($method);

        if ($parts[0] == 'add' && isset($parts[1]))
        {
            $config = isset($args[0]) ? $args[0] : array();
            $command = $this->addCommand(strtolower($parts[1]), $config);
            return $command;
        }

        return parent::__call($method, $args);
    }
}
