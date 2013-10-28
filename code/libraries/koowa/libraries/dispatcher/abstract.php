<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
abstract class KDispatcherAbstract extends KControllerAbstract implements KDispatcherInterface
{
	/**
	 * Controller object or identifier (com://APP/COMPONENT.controller.NAME)
	 *
	 * @var	string|object
	 */
	protected $_controller;

	/**
	 * Constructor.
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		//Set the controller
		$this->_controller = $config->controller;

		if(KRequest::method() != 'GET') {
			$this->registerCallback('after.dispatch' , array($this, 'forward'));
	  	}

	    $this->registerCallback('after.dispatch', array($this, 'render'));
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
    	$config->append(array(
        	'controller' => $this->getIdentifier()->package,
    		'request'	 => KRequest::get('get', 'string'),
        ))->append(array (
            'request' 	 => array('format' => KRequest::format() ? KRequest::format() : 'html')
        ));

        parent::_initialize($config);
    }

	/**
	 * Method to get a controller object
	 *
	 * @return	KControllerAbstract
	 */
	public function getController()
	{
		if(!($this->_controller instanceof KControllerInterface))
		{
		    //Make sure we have a controller identifier
		    if(!($this->_controller instanceof KObjectIdentifier)) {
		        $this->setController($this->_controller);
			}

		    $config = array(
        		'request' 	   => $this->_request->query->toArray(),
			    'dispatched'   => true
        	);

			$this->_controller = $this->getObject($this->_controller, $config);
		}

		return $this->_controller;
	}

	/**
	 * Method to set a controller object attached to the dispatcher
	 *
	 * @param	mixed	$controller An object that implements KObjectInterface, KObjectIdentifier object
	 * 					or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a controller identifier
	 * @return	KDispatcherAbstract
	 */
	public function setController($controller)
	{
		if(!($controller instanceof KControllerInterface))
		{
			if(is_string($controller) && strpos($controller, '.') === false )
		    {
		        // Controller names are always singular
			    if(KStringInflector::isPlural($controller)) {
				    $controller = KStringInflector::singularize($controller);
			    }

			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('controller');
			    $identifier->name	= $controller;
			}
		    else $identifier = $this->getIdentifier($controller);

			if($identifier->path[0] != 'controller') {
				throw new UnexpectedValueException('Identifier: '.$identifier.' is not a controller identifier');
			}

			$controller = $identifier;
		}

		$this->_controller = $controller;

		return $this;
	}

	/**
	 * Dispatch the controller
	 *
	 * @param   KCommandContext $context A command context object
	 * @return	mixed
	 */
	protected function _actionDispatch(KCommandContext $context)
	{
	    $action = KRequest::get('post.action', 'cmd', strtolower(KRequest::method()));

	    if(KRequest::method() != KHttpRequest::GET) {
            $context->data = KRequest::get(strtolower(KRequest::method()), 'raw');;
        }

	    $result = $this->getController()->execute($action, $context);

        return $result;
	}

	/**
	 * Forward after a post request
	 *
	 * Either do a redirect or a execute a browse or read action in the controller
	 * depending on the request method and type
	 *
     * @param   KCommandContext $context A command context object
	 * @return mixed
	 */
	protected function _actionForward(KCommandContext $context)
	{
		if (KRequest::type() == 'HTTP')
		{
			if($redirect = $this->getController()->getRedirect())
			{
			    JFactory::getApplication()
					->redirect($redirect['url'], $redirect['message'], $redirect['type']);
			}
		}

		if(KRequest::type() == 'AJAX')
		{
			$context->result = $this->getController()->execute('display', $context);
			return $context->result;
		}
	}

	/**
	 * Push the controller data into the document
	 *
	 * This function diverts the standard behavior and will push specific controller data
	 * into the document
	 *
     * @param   KCommandContext $context A command context object
	 * @return	mixed
	 */
	protected function _actionRender(KCommandContext $context)
	{
	    //Headers
	    if($context->headers)
	    {
	        foreach($context->headers as $name => $value) {
	            header($name.' : '.$value);
	        }
	    }

	    //Status
        if($context->status) {
            // FIXME: Replace with proper method call
            header('HTTP/1.1 '.$context->status.' '.KHttpResponse::$status_messages[$context->status]);
        }

	    if(is_string($context->result)) {
		     return $context->result;
		}
	}
}
