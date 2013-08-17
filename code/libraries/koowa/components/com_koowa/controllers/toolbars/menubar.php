<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Menu Controller Toolbar
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaControllerToolbarMenubar extends KControllerToolbarAbstract
{
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
            'type'  => 'menubar',
        ));

        parent::_initialize($config);
    }

    /**
     * Add a command
     *
     * Disable the menubar only for singular views that are editable.
     *
     * @param   string	$name   The command name
     * @param	mixed	$config Parameters to be passed to the command
     * @return  KControllerToolbarInterface
     */
    public function addCommand($name, $config = array())
    {
        $command = parent::addCommand($name, $config);

        $controller = $this->getController();

        if($controller->isEditable() && KInflector::isSingular($controller->getView()->getName())) {
            $command->disabled = true;
        }

        return $this;
    }

    /**
	 * Get the list of commands
	 *
	 * Will attempt to use information from the xml manifest if possible
	 *
	 * @return  array
	 */
	public function getCommands()
	{
	    $name     = $this->getController()->getIdentifier()->name;
	    $package  = $this->getIdentifier()->package;
	    $manifest = JPATH_ADMINISTRATOR.'/components/com_'.$package.'/'.$package.'.xml';

	    if(file_exists($manifest))
	    {
	        $xml = simplexml_load_file($manifest);

	        if(isset($xml->administration->submenu))
	        {
	            foreach($xml->administration->submenu->children() as $menu)
	            {
	                $view = (string)$menu['view'];

	                $this->addCommand((string)$menu, array(
	            		'href'   => JRoute::_('index.php?option=com_'.$package.'&view='.$view),
	            		'active' => ($name == KInflector::singularize($view))
	                ));
	            }
	        }
	    }

	    return parent::getCommands();
	}
}
