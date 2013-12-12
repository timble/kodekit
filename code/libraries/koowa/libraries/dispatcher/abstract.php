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
            'request'    => 'koowa:dispatcher.request',
            'response'   => 'koowa:dispatcher.response',
            'user'       => 'koowa:dispatcher.user',
        ));

        parent::_initialize($config);
    }

    /**
     * Get the request object
     *
     * @throws	UnexpectedValueException	If the request doesn't implement the KDispatcherRequestInterface
     * @return KDispatcherRequest
     */
    public function getRequest()
    {
        if(!$this->_request instanceof KDispatcherRequestInterface)
        {
            $this->_request = parent::getRequest();

            if(!$this->_request instanceof KDispatcherRequestInterface)
            {
                throw new UnexpectedValueException(
                    'Request: '.get_class($this->_request).' does not implement KDispatcherRequestInterface'
                );
            }
        }

        return $this->_request;
    }

    /**
     * Get the response object
     *
     * @throws	UnexpectedValueException	If the response doesn't implement the KDispatcherResponseInterface
     * @return KDispatcherResponse
     */
    public function getResponse()
    {
        if(!$this->_response instanceof KDispatcherResponseInterface)
        {
            $this->_response = parent::getResponse();

            //Set the request in the response
            $this->_response->setRequest($this->getRequest());

            if(!$this->_response instanceof KDispatcherResponseInterface)
            {
                throw new UnexpectedValueException(
                    'Response: '.get_class($this->_response).' does not implement KDispatcherResponseInterface'
                );
            }
        }

        return $this->_response;
    }

    /**
     * Get the user object
     *
     * @throws	UnexpectedValueException	If the user doesn't implement the KDispatcherUserInterface
     * @return KDispatcherUserInterface
     */
    public function getUser()
    {
        if(!$this->_user instanceof KDispatcherUserInterface)
        {
            $this->_user = parent::getUser();

            if(!$this->_user instanceof KDispatcherUserInterface)
            {
                throw new UnexpectedValueException(
                    'User: '.get_class($this->_user).' does not implement KDispatcherUserInterface'
                );
            }
        }

        return $this->_user;
    }

    /**
     * Method to get a controller object
     *
     * @throws	UnexpectedValueException	If the controller doesn't implement the ControllerInterface
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
                'request' 	 => $this->getRequest(),
                'response'   => $this->getResponse(),
                'user'       => $this->getUser(),
                'dispatched' => true
        	);

			$this->_controller = $this->getObject($this->_controller, $config);

            //Make sure the controller implements KControllerInterface
            if(!$this->_controller instanceof KControllerInterface)
            {
                throw new UnexpectedValueException(
                    'Controller: '.get_class($this->_controller).' does not implement KControllerInterface'
                );
            }
		}

		return $this->_controller;
	}

    /**
     * Method to set a controller object attached to the dispatcher
     *
     * @param	mixed	$controller An object that implements KControllerInterface, KObjectIdentifier object
     * 					            or valid identifier string
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

			$controller = $identifier;
		}

		$this->_controller = $controller;

		return $this;
	}

    /**
     * Get the controller context
     *
     * @return KDispatcherContext
     */
    public function getContext()
    {
        $context = new KDispatcherContext();

        $context->setSubject($this);
        $context->setRequest($this->getRequest());
        $context->setResponse($this->getResponse());
        $context->setUser($this->getUser());

        return $context;
    }

    /**
     * Forward the request
     *
     * Forward to another dispatcher internally. Method makes an internal sub-request, calling the specified
     * dispatcher and passing along the context.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @throws	UnexpectedValueException	If the dispatcher doesn't implement the KDispatcherInterface
     */
    protected function _actionForward(KDispatcherContextInterface $context)
    {
        //Get the dispatcher identifier
        if(is_string($context->param) && strpos($context->param, '.') === false )
        {
            $identifier			 = clone $this->getIdentifier();
            $identifier->package = $context->param;
        }
        else $identifier = $this->getIdentifier($context->param);

        //Create the dispatcher
        $config = array(
            'request' 	 => $context->request,
            'response'   => $context->response,
            'user'       => $context->user,
        );

        $dispatcher = $this->getObject($identifier, $config);

        if(!$dispatcher instanceof KDispatcherInterface)
        {
            throw new UnexpectedValueException(
                'Dispatcher: '.get_class($dispatcher).' does not implement KDispatcherInterface'
            );
        }

        $dispatcher->dispatch($context);
    }

    /**
     * Dispatch the request
     *
     * Dispatch to a controller internally. Functions makes an internal sub-request, based on the information in
     * the request and passing along the context.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return	mixed
     */
    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        //Send the response
        $this->send($context);
    }

    /**
     * Send the response
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        $context->response->send();
        exit(0);
    }
}
