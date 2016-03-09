<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
/**
 * Abstract Dispatcher
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Dispatcher
 */
abstract class KDispatcherAbstract extends KControllerAbstract implements KDispatcherInterface
{
    /**
     * Controller object or identifier
     *
     * @var	string|object
     */
    protected $_controller;

    /**
     * Controller action
     *
     * @var	string
     */
    protected $_controller_action;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config	An optional ObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the controller
        $this->_controller = $config->controller;

        //Set the controller action
        $this->_controller_action = $config->controller_action;

        //Resolve the request
        $this->addCommandCallback('before.dispatch', '_resolveRequest');

        //Register the default exception handler
        $this->addEventListener('onException', array($this, 'fail'));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config 	An optional ObjectConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller'        => $this->getIdentifier()->package,
            'controller_action' => 'render',
            'authenticators'    => array()
        ))->append(array(
            'behaviors'     => array('authenticatable' => array('authenticators' => $config->authenticators)),
        ));

        parent::_initialize($config);
    }

    /**
     * Get the request object
     *
     * @throws	UnexpectedValueException	If the request doesn't implement the DispatcherRequestInterface
     * @return KDispatcherRequest
     */
    public function getRequest()
    {
        if(!$this->_request instanceof KDispatcherRequestInterface)
        {
            //Setup the request
            $this->_request->user = $this->getUser();

            $this->_request = $this->getObject('dispatcher.request', KObjectConfig::unbox($this->_request));

            if(!$this->_request instanceof KDispatcherRequestInterface)
            {
                throw new UnexpectedValueException(
                    'Request: '.get_class($this->_request).' does not implement DispatcherRequestInterface'
                );
            }
        }

        return $this->_request;
    }

    /**
     * Get the response object
     *
     * @throws	UnexpectedValueException	If the response doesn't implement the DispatcherResponseInterface
     * @return KDispatcherResponse
     */
    public function getResponse()
    {
        if(!$this->_response instanceof KDispatcherResponseInterface)
        {
            //Setup the response
            $this->_response->request  = $this->getRequest();
            $this->_response->user     = $this->getUser();

            $this->_response = $this->getObject('dispatcher.response', KObjectConfig::unbox($this->_response));

            if(!$this->_response instanceof KDispatcherResponseInterface)
            {
                throw new \UnexpectedValueException(
                    'Response: '.get_class($this->_response).' does not implement DispatcherResponseInterface'
                );
            }
        }

        return $this->_response;
    }

    /**
     * Method to get a controller object
     *
     * @throws	UnexpectedValueException	If the controller doesn't implement the ControllerInterface
     * @return	KControllerInterface
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
                'user'       => $this->getUser(),
                'response'   => $this->getResponse(),
                'dispatched' => true
            );

            $this->_controller = $this->getObject($this->_controller, $config);

            //Make sure the controller implements ControllerInterface
            if(!$this->_controller instanceof KControllerInterface)
            {
                throw new \UnexpectedValueException(
                    'Controller: '.get_class($this->_controller).' does not implement ControllerInterface'
                );
            }
        }

        return $this->_controller;
    }

    /**
     * Method to set a controller object attached to the dispatcher
     *
     * @param	mixed	$controller An object that implements ControllerInterface, ObjectIdentifier object
     * 					            or valid identifier string
     * @param  array  $config  An optional associative array of configuration options
     * @return	$this
     */
    public function setController($controller, $config = array())
    {
        if(!($controller instanceof KControllerInterface))
        {
            if(is_string($controller) && strpos($controller, '.') === false )
            {
                // Controller names are always singular
                if(KStringInflector::isPlural($controller)) {
                    $controller = KStringInflector::singularize($controller);
                }

                $identifier			= $this->getIdentifier()->toArray();
                $identifier['path']	= array('controller');
                $identifier['name']	= $controller;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($controller);

            //Set the configuration
            $identifier->getConfig()->append($config);

            $controller = $identifier;
        }

        $this->_controller = $controller;

        return $this;
    }

    /**
     * Method to get a controller action to be executed
     *
     * @return	string
     */
    public function getControllerAction()
    {
        return $this->_controller_action;
    }

    /**
     * Method to set the controller action to be executed
     *
     * @return	$this
     */
    public function setControllerAction($action)
    {
        $this->_controller_action = $action;
        return $this;
    }

    /**
     * Get the controller context
     *
     * @return  KCommand
     */
    public function getContext()
    {
        $context = new KDispatcherContext();

        $context->setSubject($this);
        $context->setRequest($this->getRequest());
        $context->setUser($this->getUser());
        $context->setResponse($this->getResponse());

        return $context;
    }

    /**
     * Resolve the request
     *
     * @param KDispatcherContextInterface $context A dispatcher context object
     */
    protected function _resolveRequest(KDispatcherContextInterface $context)
    {
        //Resolve the controller
        if($context->request->query->has('view')) {
            $this->setController($context->request->query->get('view', 'cmd'));
        }

        //Resolve the controller action
        if($context->request->data->has('_action')) {
            $this->setControllerAction($context->request->data->get('_action', 'cmd'));
        }
    }

    /**
     * Dispatch the request
     *
     * Dispatch to a controller internally or forward to another component.  Functions makes an internal sub-request,
     * based on the information in the request and passing along the context.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return	mixed
     */
    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        $controller = $this->getController();
        $action     = $this->getControllerAction();

        //Execute the component and pass along the context
        $controller->execute($action, $context);

        //Send the response
        return $this->send($context);
    }

    /**
     * Redirect
     *
     * Redirect to a URL externally. Method performs a 301 (permanent) redirect. Method should be used to immediately
     * redirect the dispatcher to another URL after a GET request.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    protected function _actionRedirect(KDispatcherContextInterface $context)
    {
        $url = $context->param;

        $context->response->setStatus(KDispatcherResponse::MOVED_PERMANENTLY);
        $context->response->setRedirect($url);

        //Send the response
        return $this->send($context);
    }

    /**
     * Handle errors and exceptions
     *
     * @throws \InvalidArgumentException If the action parameter is not an instance of Exception or ExceptionError
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    protected function _actionFail(KDispatcherContextInterface $context)
    {
        //Check an exception was passed
        if(!isset($context->param) && !$context->param instanceof Exception)
        {
            throw new \InvalidArgumentException(
                "Action parameter 'exception' [Exception] is required"
            );
        }

        //Get the exception object
        if($context->param instanceof KEventException) {
            $exception = $context->param->getException();
        } else {
            $exception = $context->param;
        }

        //If the error code does not correspond to a status message, use 500
        $code = $exception->getCode();
        if(!isset(KHttpResponse::$status_messages[$code])) {
            $code = '500';
        }

        //Get the error message
        $message = $exception->getMessage();
        if(empty($message)) {
            $message = KHttpResponse::$status_messages[$code];
        }

        //Store the exception in the context
        $context->exception = $exception;

        //Set the response status
        $context->response->setStatus($code , $message);

        //Send the response
        return $this->send($context);
    }

    /**
     * Send the response
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return mixed
     */
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        $context->response->send();
    }
}
