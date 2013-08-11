<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Controller Toolbar
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerToolbarAbstract extends KObject
{
    /**
     * The toolbar title
     *
     * @var     string
     */
    protected $_title = '';

    /**
     * The toolbar icon
     *
     * @var     string
     */
    protected $_icon = '';

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
     * Constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();

        parent::__construct($config);

        // Set the controller
        $this->_controller = $config->controller;

        // Set the title
        $this->setTitle($config->title);

        // Set the icon
        $this->setIcon($config->icon);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'title'         => KInflector::humanize($this->getName()),
            'icon'          => $this->getName(),
            'controller'    => null,
        ));

        parent::_initialize($config);
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
     * Get the toolbar's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getIdentifier()->name;
    }

    /**
     * Set the toolbar's title
     *
     * @param   string  $title Title
     * @return  KControllerToolbarAbstract
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

 	/**
     * Get the toolbar's title
     *
     * @return   string  Title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set the toolbar's icon
     *
     * @param   string  $icon Icon
     * @return  KControllerToolbarAbstract
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
        return $this;
    }

	/**
     * Get the toolbar's icon
     *
     * @return   string  Icon
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * Add a separator
     *
     * @return  KControllerToolbarAbstract
     */
    public function addSeparator()
    {
        $this->_commands[] = new KControllerToolbarCommand('separator');
        return $this;
    }

    /**
     * Add a command
     *
     * @param   string	$name   The command name
     * @param	mixed	$config Parameters to be passed to the command
     * @return  KControllerToolbarAbstract
     */
    public function addCommand($name, $config = array())
    {
        //Create the config object
        $command = new KControllerToolbarCommand($name, $config);

        //Find the command function to call
        if(method_exists($this, '_command'.ucfirst($name)))
        {
            $function =  '_command'.ucfirst($name);
            $this->$function($command);
        }
        else
        {
            //Don't set an action for GET commands
            if(!isset($command->attribs->href))
            {
                $command->append(array(
         			'attribs'    => array(
               			'data-action'  => $command->getName()
                    )
                ));
            }
        }

        $this->_commands[$name] = $command;
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
     * Reset the commands array
     *
     * @return  KControllerToolbarAbstract
     */
    public function reset()
    {
        $this->_commands = array();
        return $this;
    }

 	/**
     * Add a command by it's name
	 *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return mixed The result of the function
     *
     * @see addCommand()
     */
    public function __call($method, $args)
    {
		$parts = KInflector::explode($method);

		if($parts[0] == 'add' && isset($parts[1]))
		{
		    $config = isset($args[0]) ? $args[0] : array();
		    $this->addCommand(strtolower($parts[1]), $config);
			return $this;
		}

        return parent::__call($method, $args);
    }
}
