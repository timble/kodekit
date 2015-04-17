<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Toolbar Command
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar
 */
class KControllerToolbarCommand extends KObjectConfig implements KControllerToolbarCommandInterface
{
    /**
     * The command name
     *
     * @var string
     */
    protected $_name;

    /**
     * The commands
     *
     * @var string|object
     */
    protected $_commands = null;

    /**
     * Toolbar command object
     *
     * @var object
     */
    protected $_parent = null;

    /**
     * Toolbar object
     *
     * @var KControllerToolbarCommandInterface
     */
    protected $_toolbar = null;

    /**
     * Constructor.
     *
     * @param   string              $name   The command name
     * @param   array|KObjectConfig $config An associative array of configuration settings or a KObjectConfig instance.
     */
    public function __construct( $name, $config = array() )
    {
        parent::__construct($config);

        $this->append(array(
            'icon'       => 'icon-32-'.$name,
            'id'         => $name,
            'label'      => ucfirst($name),
            'disabled'   => false,
            'title'      => '',
            'href'       => null,
            'attribs'    => array(
                'class'        => array(),
            )
        ));

        //Create the children array
        $this->_commands = array();

        //Set the command name
        $this->_name = $name;
    }

    /**
     * Get the command name
     *
     * @return string	The command name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Add a command
     *
     * @param   string  $command The command name
     * @param   mixed   $config  Parameters to be passed to the command
     * @return  KControllerToolbarCommand  The command that was added
     */
    public function addCommand($command, $config = array())
    {
        if (!($command instanceof KControllerToolbarCommandInterface)) {
            $command = $this->getCommand($command, $config);
        }

        //Set the command parent
        $command->setParent($this);

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
        return $this->getToolbar()->getCommand($name, $config);
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
     * Get the list of commands
     *
     * @return  array
     */
    public function getCommands()
    {
        return $this->_commands;
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
     * Get a new iterator
     *
     * @return  RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new RecursiveArrayIterator($this->getCommands());
    }

    /**
     * Set a configuration item
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $value = new KObjectConfig($value);
        }

        parent::set($name, $value);
    }

    /**
     * Get the parent node
     *
     * @return	KControllerToolbarCommandInterface
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Set the parent command
     *
     * @param KControllerToolbarCommandInterface $command The parent command
     * @return KControllerToolbarCommand
     */
    public function setParent(KControllerToolbarCommandInterface $command )
    {
        $this->_parent = $command;
        return $this;
    }

    /**
     * Get the toolbar
     *
     * @return KControllerToolbarInterface
     */
    public function getToolbar()
    {
        return $this->_toolbar;
    }

    /**
     * Set the toolbar
     *
     * @param KControllerToolbarInterface $toolbar  The toolbar this command belongs too
     * @return KControllerToolbarCommand
     */
    public function setToolbar(KControllerToolbarInterface $toolbar )
    {
        $this->_toolbar = $toolbar;
        return $this;
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

        if($parts[0] == 'add' && isset($parts[1]))
        {
            $config = isset($args[0]) ? $args[0] : array();
            $command = $this->addCommand(strtolower($parts[1]), $config);
            return $command;
        }

        return null;
    }
}
