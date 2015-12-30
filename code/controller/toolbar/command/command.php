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
    private $__commands = null;

    /**
     * Toolbar object
     *
     * @var KControllerToolbarInterface
     */
    private $__toolbar = null;

    /**
     * Constructor.
     *
     * @param	string $name The command name
     * @param   array|KObjectConfig 	An associative array of configuration settings or a ObjectConfig instance.
     */
    public function __construct( $name, $config = array() )
    {
        parent::__construct($config);

        $this->append(array(
            'icon'       => 'icon-32-'.$name,
            'id'         => $name,
            'label'      => ucfirst($name),
            'disabled'   => false,
            'title'		 => '',
            'href'       => null,
            'attribs'    => array(
                'class'  => array(),
            ),
        ));

        //Create the children array
        $this->__commands = array();

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
     * Get the toolbar's title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add a command
     *
     * @param   string	$command The command name
     * @param	mixed	$config  Parameters to be passed to the command
     * @return  KControllerToolbarCommand  The command that was added
     */
    public function addCommand($command, $config = array())
    {
        if (!($command instanceof KControllerToolbarCommand)) {
            $command = $this->getCommand($command, $config);
        }

        $this->__commands[$command->getName()] = $command;
        return $command;
    }

    /**
     * Get a command by name
     *
     * @param string $name  The command name
     * @param array $config An optional associative array of configuration settings
     * @return mixed ControllerToolbarCommand if found, false otherwise.
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
        return isset($this->__commands[$name]);
    }

    /**
     * Get a new iterator
     *
     * @return  \RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->__commands);
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Required by the Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->__commands);
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
     * Get the toolbar object
     *
     * @return KControllerToolbarInterface
     */
    public function getToolbar()
    {
        return $this->__toolbar;
    }

    /**
     * Set the parent node
     *
     * @param KControllerToolbarInterface $toolbar The toolbar this command belongs too
     * @return KControllerToolbarCommand
     */
    public function setToolbar(KControllerToolbarInterface $toolbar )
    {
        $this->__toolbar = $toolbar;
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
    }
}
