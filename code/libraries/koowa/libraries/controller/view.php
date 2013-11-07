<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract View Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerView extends KControllerAbstract implements KControllerViewable
{
	/**
	 * URL for redirection.
	 *
	 * @var	string
	 */
	protected $_redirect = null;

	/**
	 * Redirect message.
	 *
	 * @var	string
	 */
	protected $_redirect_message = null;

	/**
	 * Redirect message type.
	 *
	 * @var	string
	 */
	protected $_redirect_type = 'message';

	/**
	 * View object or identifier (com://APP/COMPONENT.view.NAME.FORMAT)
	 *
	 * @var	string|object
	 */
	protected $_view;

	/**
	 * Model object or identifier (com://APP/COMPONENT.model.NAME)
	 *
	 * @var	string|object
	 */
	protected $_model;

	/**
	 * Constructor
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

	    // Set the model identifier
	    $this->_model = $config->model;

		// Set the view identifier
		$this->_view = $config->view;

		//Register display as alias for get
		$this->registerActionAlias('display', 'get');
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
        $config->append(array(
    	    'model'	     => $this->getIdentifier()->name,
    	    'behaviors'  => array('permissible'),
    		'request' 	 => array('format' => 'html')
         ))->append(array(
            'view' 		=> $config->request->view ? $config->request->view : $this->getIdentifier()->name
        ));

        parent::_initialize($config);
    }

	/**
	 * Get the view object attached to the controller
	 *
	 * This function will check if the view folder exists. If not it will throw an exception. This is a security measure
     * to make sure we can only explicitly get data from views the have been physically defined.
	 *
	 * @throws  KControllerExceptionNotFound if the view cannot be found.
	 * @return	KViewAbstract
	 */
	public function getView()
	{
	    if(!$this->_view instanceof KViewInterface)
		{
		    //Make sure we have a view identifier
		    if(!($this->_view instanceof KObjectIdentifier)) {
		        $this->setView($this->_view);
			}

			//Create the view
			$config = array(
                'url' => KRequest::url(),
                'model' => $this->getModel(),
                'auto_assign' => $this instanceof KControllerModellable
            );

			$this->_view = $this->getObject($this->_view, $config);

			//Set the layout
			if(isset($this->getRequest()->query->layout)) {
        	    $this->_view->setLayout($this->getRequest()->query->layout);
        	}

			//Make sure the view exists
		    if($this->isDispatched() && !file_exists(dirname($this->_view->getIdentifier()->filepath))) {
		        throw new KControllerExceptionNotFound('View: '.$this->_view->getName().' not found', KHttpResponse::NOT_FOUND);
		    }
		}

		return $this->_view;
	}

	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param	mixed	$view An object that implements KObjectInterface, KObjectIdentifier object
	 * 					or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a view identifier
	 * @return	object	A KViewAbstract object or a KObjectIdentifier object
	 */
	public function setView($view)
	{
		if(!($view instanceof KViewInterface))
		{
			if(is_string($view) && strpos($view, '.') === false )
		    {
			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('view', $view);
			    $identifier->name	= $this->getRequest()->query->format;
			}
			else $identifier = $this->getIdentifier($view);

			if($identifier->path[0] != 'view') {
				throw new UnexpectedValueException('Identifier: '.$identifier.' is not a view identifier');
			}

			$view = $identifier;
		}

		$this->_view = $view;

		return $this->_view;
	}

	/**
	 * Get the model object attached to the controller
	 *
	 * @return	KModelAbstract
	 */
	public function getModel()
	{
		if(!$this->_model instanceof KModelInterface)
		{
			//Make sure we have a model identifier
		    if(!($this->_model instanceof KObjectIdentifier)) {
		        $this->setModel($this->_model);
			}

            $this->_model = $this->getObject($this->_model);

            //Inject the request into the model state
            $this->_model->setState($this->getRequest()->query->toArray());
		}

		return $this->_model;
	}

	/**
	 * Method to set a model object attached to the controller
	 *
	 * @param	mixed	$model An object that implements KObjectInterface, KObjectIdentifier object
	 * 					or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a model identifier
	 * @return	object	A KModelAbstract object or a KObjectIdentifier object
	 */
	public function setModel($model)
	{
		if(!($model instanceof KModelInterface))
		{
	        if(is_string($model) && strpos($model, '.') === false )
		    {
			    // Model names are always plural
			    if(KStringInflector::isSingular($model)) {
				    $model = KStringInflector::pluralize($model);
			    }

			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('model');
			    $identifier->name	= $model;
			}
			else $identifier = $this->getIdentifier($model);

			if($identifier->path[0] != 'model') {

				throw new UnexpectedValueException('Identifier: '.$identifier.' is not a model identifier');
			}

			$model = $identifier;
		}

		$this->_model = $model;

		return $this->_model;
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @param	string  $url URL to redirect to.
	 * @param	string	$msg Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param	string	$type Message type. Optional, defaults to 'message'.
	 * @return	KControllerAbstract
	 */
	public function setRedirect( $url, $msg = null, $type = 'message' )
	{
		$this->_redirect   		 = $url;
		$this->_redirect_message = $msg;
		$this->_redirect_type	 = $type;

		return $this;
	}

	/**
	 * Returns an array with the redirect url, the message and the message type
	 *
	 * @return array Named array containing url, message and messageType, or null if no redirect was set
	 */
	public function getRedirect()
	{
		$result = array();

		if(!empty($this->_redirect))
		{
			$result = array(
				'url' 		=> JRoute::_($this->_redirect, false),
				'message' 	=> $this->_redirect_message,
				'type' 		=> $this->_redirect_type,
			);
		}

		return $result;
	}

    /**
     * Specialised display function.
     *
     * @param KControllerContextInterface $context A command context object
     * @return    string|bool    The rendered output of the view or false if something went wrong
     */
	protected function _actionGet(KControllerContextInterface $context)
	{
	    $result = $this->getView()->display();
	    return $result;
	}

	/**
	 * Supports a simple form Fluent Interfaces. Allows you to set the request properties by using the request property
     * name as the method name.
	 *
	 * For example : $controller->view('name')->limit(10)->browse();
	 *
	 * @param	string	$method Method name
	 * @param	array	$args   Array containing all the arguments for the original call
	 * @return	mixed
	 * @see http://martinfowler.com/bliki/FluentInterface.html
	 */
	public function __call($method, $args)
	{
	    //Check first if we are calling a mixed in method.
	    //This prevents the model being loaded during object instantiation.
		if(!isset($this->_mixed_methods[$method]))
        {
            //Check if the method is a state property
			$state = $this->getModel()->getState();

			if(isset($state->$method) || in_array($method, array('layout', 'view', 'format')))
			{
                $this->getRequest()->query->$method = $args[0];
                $this->getModel()->getState()->set($method, $args[0]);

				if($method == 'view') {
                   $this->_view = $args[0];
                }

				return $this;
			}
        }

		return parent::__call($method, $args);
	}
}
