<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Commandable Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
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
	 * @param	mixed	$menubar An object that implements KObjectInterface, KServiceIdentifier object
	 * 					         or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a view identifier
	 * @return	KControllerToolbarAbstract
	 */
    public function setMenubar($menubar)
    {
        if(!($menubar instanceof KControllerToolbarInterface))
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

    protected function _beforeGet(KCommandContext $context)
    {
        if (JFactory::getApplication()->isSite() && $this->isDispatched())
        {
            if ($this->getView() instanceof KViewHtml && $this->getRequest()->layout === 'form')
            {
                $this->_render = array_merge($this->_render, array('toolbar'));
            }
        }

        parent::_beforeGet($context);
    }

    /**
	 * Run the toolbar filter to convert toolbars and menubars to HTML in the template
	 * .
     * @param   KCommandContext	$context A command context object
	 */
    protected function _afterGet(KCommandContext $context)
    {
        if ($this->isDispatched() && $this->getView() instanceof KViewHtml)
        {
            $filter = $this->getView()->getTemplate()->getFilter('toolbar');

            $filter->setRenderers($this->_render);
            $filter->setToolbar($this->getToolbar());
            $filter->setMenubar($this->getMenubar());

            $result = $context->result;

            $filter->write($result);

            $context->result = $result;
        }
    }
}
