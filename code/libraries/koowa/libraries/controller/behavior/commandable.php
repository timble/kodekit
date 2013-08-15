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
 * @package Koowa\Library\Controller
 */
class KControllerBehaviorCommandable extends KControllerBehaviorAbstract
{
	/**
	 * Toolbar object or identifier (com://APP/COMPONENT.model.NAME)
	 *
	 * @var	string|object
	 */
	protected $_toolbar;

	/**
	 * Constructor
	 *
	 * @param   KConfig $config Configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_toolbar = $config->toolbar;
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
    		'toolbar'	=> null,
        ));

        parent::_initialize($config);
    }

	/**
	 * Get the view object attached to the controller
	 *
	 * @return	KControllerToolbarAbstract
	 */
    public function getToolbar()
    {
        if(!$this->_toolbar instanceof KControllerToolbarAbstract)
		{
		    //Make sure we have a view identifier
		    if(!($this->_toolbar instanceof KServiceIdentifier)) {
		        $this->setToolbar($this->_toolbar);
			}

			$config = array(
			    'controller' => $this->getMixer()
			);

			$this->_toolbar = $this->getService($this->_toolbar, $config);
		}

        return $this->_toolbar;
    }

	/**
	 * Method to set a toolbar object attached to the controller
	 *
	 * @param	mixed	$toolbar An object that implements KObjectInterface, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a view identifier
	 * @return	KControllerToolbarAbstract
	 */
    public function setToolbar($toolbar)
    {
        if(!($toolbar instanceof KControllerToolbarAbstract))
		{
			if(is_string($toolbar) && strpos($toolbar, '.') === false )
		    {
			    $identifier         = clone $this->getIdentifier();
                $identifier->path   = array('controller', 'toolbar');
                $identifier->name   = $toolbar;
			}
			else $identifier = $this->getIdentifier($toolbar);

			if($identifier->path[1] != 'toolbar') {
				throw new UnexpectedValueException('Identifier: '.$identifier.' is not a toolbar identifier');
			}

			$toolbar = $identifier;
		}

		$this->_toolbar = $toolbar;

        return $this;
    }

    /**
	 * Add default toolbar commands
	 * .
	 * @param	KCommandContext	$context A command context object
	 */
    protected function _beforeGet(KCommandContext $context)
    {
        if(!$this->_toolbar) {
            $this->setToolbar($this->getView()->getName());
        }
    }

	/**
	 * Add default toolbar commands and set the toolbar title
	 * .
	 * @param	KCommandContext	$context A command context object
	 */
    protected function _afterRead(KCommandContext $context)
    {
        if($this->_toolbar)
        {
            $name = ucfirst($context->caller->getIdentifier()->name);

            if($this->getModel()->getState()->isUnique())
            {
                $saveable = $this->canEdit();
                $title    = 'Edit '.$name;
            }
            else
            {
                $saveable = $this->canAdd();
                $title    = 'New '.$name;
            }

            if($saveable)
            {
                $this->getToolbar()
                     ->setTitle($title)
                     ->addCommand('save')
                     ->addCommand('apply');
            }

            $this->getToolbar()->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));
        }
    }

    /**
	 * Add default toolbar commands
	 * .
	 * @param	KCommandContext	$context A command context object
	 */
    protected function _afterBrowse(KCommandContext $context)
    {
        if($this->_toolbar)
        {
            if($this->canAdd())
            {
                $identifier = $context->caller->getIdentifier();
                $config     = array('attribs' => array(
                    				'href' => JRoute::_( 'index.php?option=com_'.$identifier->package.'&view='.$identifier->name)
                              ));

                $this->getToolbar()->addCommand('new', $config);
            }

            if($this->canDelete()) {
                $this->getToolbar()->addCommand('delete');
            }
        }
    }
}
