<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Dispatcher
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract controller dispatcher
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Dispatcher
 * @uses		KMixinClass
 * @uses        KObject
 * @uses        KFactory
 */
abstract class KDispatcherAbstract extends KObject implements KFactoryIdentifiable
{
	/**
	 * The default view
	 *
	 * @var string
	 */
	protected $_default_view;
	
	/**
	 * The default layout
	 *
	 * @var string
	 */
	protected $_default_layout;

	/**
	 * The object identifier
	 *
	 * @var object
	 */
	protected $_identifier = null;

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_view'
	 */
	public function __construct(array $options = array())
	{
        // Set the objects identifier
        $this->_identifier = $options['identifier'];

		// Initialize the options
        $options  = $this->_initialize($options);

        // Set the default view
        $this->_default_view = $options['default_view'];
        
        // Set the default layout
        $this->_default_layout = $options['default_layout'];
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param	array	Options
     * @return 	array	Options
     */
    protected function _initialize(array $options)
    {
        $defaults = array(
        	'default_view'   => $this->_identifier->name,
        	'default_layout' => 'default',
        	'identifier'	 => null
        );

        return array_merge($defaults, $options);
    }

	/**
	 * Get the identifier
	 *
	 * @return 	object A KFactoryIdentifier object
	 * @see 	KFactoryIdentifiable
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}

	/**
	 * Dispatch the controller and redirect
	 *
	 * @return	this
	 */
	public function dispatch()
	{
		// Get the view, and use the default view if it doesn't exist
		$view  = KRequest::get('get.view', 'cmd', $this->_default_view);
		
		// Get the layout, and use the default layout if it doesn't exist
		$layout = KRequest::get('get.layout', 'cmd', $this->_default_layout);

        // Push the view back in the request
        KRequest::set('get.view', $view);
        
        // Push the layout back in the request
        KRequest::set('get.layout', $layout);

        //Get/Create the controller
        $controller = $this->getController();

        // Perform the Request action
        $action  = KRequest::get('request.action', 'cmd', null);

        //Execute the controller, handle exeception if thrown.
        try
        {
        	$controller->execute($action);
        }
        catch (KControllerException $e)
        {
        	if($e->getCode() == KHttp::STATUS_UNAUTHORIZED)
        	{
				KFactory::get('lib.koowa.application')
					->redirect( 'index.php', JText::_($e->getMessage()) );
        	}
        	else
        	{
        		// rethrow, we don't know what to do with other error codes yet
        		throw $e;
        	}
        }

		// Redirect if set by the controller
		if($redirect = $controller->getRedirect())
		{
			KFactory::get('lib.joomla.application')
				->redirect($redirect['url'], $redirect['message'], $redirect['messageType']);
		}

		return $this;
	}


	/**
	 * Method to get a controller object
	 *
	 * @return	object	The controller.
	 */
	protected function getController(array $options = array())
	{
		$application 	= $this->_identifier->application;
		$package 		= $this->_identifier->package;
		
		$view 			= KRequest::get('get.view', 'cmd');
		$controller 	= KRequest::get('get.controller', 'cmd', $view);

		//In case we are loading a subview, we use the first part of the name as controller name
		if(strpos($controller, '.') !== false)
		{
			$result = explode('.', $controller);

			//Set the controller based on the parent
			$controller = $result[0];
		}

		// Controller names are always singular
		if(KInflector::isPlural($controller)) {
			$controller = KInflector::singularize($controller);
		}

		return KFactory::get($application.'::com.'.$package.'.controller.'.$controller, $options);
	}
}
