<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Commandable Controller Behavior Class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaControllerBehaviorCommandable  extends KControllerBehaviorCommandable
{
	/**
	 * Menubar object or identifier (com://APP/COMPONENT.model.NAME)
	 *
	 * @var	string|object
	 */
	protected $_menubar;

	/**
	 * Array of parts to render
	 *
	 * @var array
	 */
	protected $_render;

	/**
	 * Constructor
	 *
	 * @param   KConfig $config Configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		// Set the view identifier
		$this->_menubar = $config->menubar;
		$this->_render  = KConfig::unbox($config->render);
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'menubar' => 'menubar',
    	    'render'  => JFactory::getApplication()->isAdmin() ? array('toolbar', 'menubar', 'title') : array()
        ));

        parent::_initialize($config);
    }

	/**
	 * Get the menubar object
	 *
	 * @return	KControllerToolbarAbstract
	 */
    public function getMenubar()
    {
        if(!$this->_menubar instanceof KControllerToolbarAbstract)
		{
		    //Make sure we have a view identifier
		    if(!($this->_menubar instanceof KServiceIdentifier)) {
		        $this->setMenubar($this->_menubar);
			}

			$config = array(
			    'controller' => $this->getMixer()
			);

			$this->_menubar = $this->getService($this->_menubar, $config);
		}

        return $this->_menubar;
    }

	/**
	 * Method to set a menubar object attached to the controller
	 *
	 * @param	mixed	$menubar An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a view identifier
	 * @return	KControllerToolbarAbstract
	 */
    public function setMenubar($menubar)
    {
        if(!($menubar instanceof KControllerToolbarAbstract))
		{
			if(is_string($menubar) && strpos($menubar, '.') === false )
		    {
			    $identifier         = clone $this->getIdentifier();
                $identifier->path   = array('controller', 'toolbar');
                $identifier->name   = $menubar;
			}
			else $identifier = $this->getIdentifier($menubar);

			if($identifier->path[1] != 'toolbar') {
				throw new UnexpectedValueException('Identifier: '.$identifier.' is not a toolbar identifier');
			}

			$menubar = $identifier;
		}

		$this->_menubar = $menubar;

        return $this;
    }

    /**
	 * Add default toolbar commands
	 * .
     * @param   KCommandContext	$context A command context object
	 */
    protected function _afterGet(KCommandContext $context)
    {
        if($this->isDispatched() && ($this->getView() instanceof KViewHtml))
        {
            //Render the toolbar
	        $document = JFactory::getDocument();

            if(in_array('toolbar', $this->_render))
            {
                $config   = array('toolbar' => $this->getToolbar());
	            $toolbar = $this->getView()->getTemplate()->getHelper('toolbar')->render($config);
            }
            else $toolbar = false;

            $document->setBuffer($toolbar, 'modules', 'toolbar');

            //Render the title
            if(in_array('title', $this->_render))
            {
                $config   = array('toolbar' => $this->getToolbar());
                $title = $this->getView()->getTemplate()->getHelper('toolbar')->title($config);
            }
            else $title = false;

            $document->setBuffer($title, 'modules', 'title');

            //Render the menubar
            if(in_array('menubar', $this->_render))
            {
                $config = array('menubar' => $this->getMenubar());
                $menubar = $this->getView()->getTemplate()->getHelper('menubar')->render($config);
            }
            else $menubar = false;

            $document->setBuffer($menubar, 'modules', 'submenu');
        }
    }
}
