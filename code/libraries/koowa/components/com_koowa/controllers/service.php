<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Service Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaControllerService extends KControllerService
{
	/**
	 * The limit information
	 *
	 * @var	array
	 */
	protected $_limit;

	/**
	 * Constructor
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		$this->_limit = $config->limit;

        // Mixin the toolbar interface
        $this->mixin(new KControllerToolbarMixin($config->append(array('mixer' => $this))));

        //Attach the toolbars
        $this->registerCallback('before.get' , array($this, 'attachToolbars'));

        if($this->isDispatched() && $config->persistable) {
            $this->addBehavior('persistable');
        }
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        //Disable controller persistency on non-HTTP requests,
        //e.g. AJAX, and requests containing the tmpl variable set to component (modal boxes)
        if($this->getIdentifier()->application === 'admin')
        {
            $persistable = (KRequest::type() == 'HTTP' && KRequest::get('get.tmpl','cmd') != 'component');
            $config->append(array(
                'persistable'    => $persistable,
            ));
        }

        //Set the maximum list limit to 100
        $config->append(array(
            'limit' => array('max' => 100, 'default' => JFactory::getApplication()->getCfg('list_limit'))
        ));

        parent::_initialize($config);
    }

    /**
     * Attach the toolbars to the controller
     *
     * void
     */
    public function attachToolbars()
    {
        if($this->getView() instanceof KViewHtml)
        {
            if($this->isDispatched() && !JFactory::getUser()->guest)
            {
                $this->attachToolbar($this->getView()->getName());

                if($this->getIdentifier()->application === 'admin') {
                    $this->attachToolbar('menubar');
                };
            }

            if($toolbars = $this->getToolbars())
            {
                $this->getView()
                    ->getTemplate()
                    ->addFilter('toolbar', array('toolbars' => $toolbars));
            };
        }
    }

    /**
     * Display action
     *
     * If the controller was not dispatched manually load the languages files
     *
     * @param   KCommandContext $context A command context object
     * @return 	string|bool 	The rendered output of the view or FALSE if something went wrong
     */
    protected function _actionGet(KCommandContext $context)
    {
        $this->getObject('translator')->loadLanguageFiles($this->getIdentifier());
        return parent::_actionGet($context);
    }

	/**
     * Browse action
     *
     * Use the application default limit if no limit exists in the model and limit the limit to a maximum.
     *
     * @param   KCommandContext $context A command context object
     * @return 	KDatabaseRowsetInterface	A rowset object containing the selected rows
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        if($this->isDispatched())
        {
            $limit = $this->getModel()->get('limit');

            //If limit is empty use default
            if(empty($limit)) {
                $limit = $this->_limit->default;
            }

            //Force the maximum limit
            if($limit > $this->_limit->max) {
                $limit = $this->_limit->max;
            }

            $this->limit = $limit;
        }

        return parent::_actionBrowse($context);
    }

    /**
     * Read action
     *
     * This functions implements an extra check to hide the main menu is the view name is singular (item views)
     *
     * @param  KCommandContext $context A command context object
     * @return KDatabaseRowInterface A row object containing the selected row
     */
    protected function _actionRead(KCommandContext $context)
    {
        //Perform the read action
        $row = parent::_actionRead($context);

        //Add the notice if the row is locked
        if($this->getIdentifier()->application === 'admin' && isset($row))
        {
            if(!isset($this->_request->layout) && $row->isLockable() && $row->locked()) {
                JFactory::getApplication()->enqueueMessage($row->lockMessage(), 'notice');
            }
        }

        return $row;
    }

	/**
     * Set a request property
     *
     *  This function translates 'limitstart' to 'offset' for compatibility with Joomla
     *
     * @param  	string 	$property The property name.
     * @param 	mixed 	$value    The property value.
     */
 	public function __set($property, $value)
    {
        if($property == 'limitstart') {
            $property = 'offset';
        }

        parent::__set($property, $value);
  	}
}
