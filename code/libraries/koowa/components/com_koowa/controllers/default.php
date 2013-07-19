<?php
/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Controller
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaControllerDefault extends KControllerService
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
	 * @param   KConfig $config Configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_limit = $config->limit;

        if($config->persistable && $this->isDispatched()) {
            $this->addBehavior('persistable');
        }
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
        /*
         * Disable controller persistency on non-HTTP requests, e.g. AJAX, and requests containing
         * the tmpl variable set to component, e.g. requests using modal boxes. This avoids
         * changing the model state session variable of the requested model, which is often
         * undesirable under these circumstances.
         */
        $config->append(array(
            'persistable'    => (JFactory::getApplication()->isAdmin() && KRequest::type() == 'HTTP' && KRequest::get('get.tmpl','cmd') != 'component'),
            'limit'          => array('max' => 100, 'default' => JFactory::getApplication()->getCfg('list_limit'))
        ));

        parent::_initialize($config);
    }

    /**
     * Display action
     *
     * If the controller was not dispatched manually load the langauges files
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
     */
    protected function _actionGet(KCommandContext $context)
    {
        $this->getService('translator')->loadLanguageFiles($this->getIdentifier());

        return parent::_actionGet($context);
    }

	/**
     * Browse action
     *
     * Use the application default limit if no limit exists in the model and limit the
     * limit to a maximum.
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
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
     * This functions implements an extra check to hide the main menu is the view name
     * is singular (item views)
     *
     *  @return KDatabaseRow    A row object containing the selected row
     */
    protected function _actionRead(KCommandContext $context)
    {
        //Perform the read action
        $row = parent::_actionRead($context);

        //Add the notice if the row is locked
        if(JFactory::getApplication()->isAdmin() && isset($row))
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
     * @param  	string 	The property name.
     * @param 	mixed 	The property value.
     */
 	public function __set($property, $value)
    {
        if($property == 'limitstart') {
            $property = 'offset';
        }

        parent::__set($property, $value);
  	}
}