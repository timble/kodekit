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
abstract class KDispatcherAbstract extends KControllerAbstract implements KDispatcherInterface
{
    /**
     * Controller object or identifier (com://APP/COMPONENT.controller.NAME)
     *
     * @var	string|object
     */
    protected $_controller;

    /**
     * List of authenticators
     *
     * Associative array of authenticators, where key holds the authenticator identifier string
     * and the value is an identifier object.
     *
     * @var array
     */
    private $__authenticators;

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

        //Add the authenticators
        $authenticators = (array) KObjectConfig::unbox($config->authenticators);

        foreach ($authenticators as $key => $value)
        {
            if (is_numeric($key)) {
                $this->addAuthenticator($value);
            } else {
                $this->addAuthenticator($key, $value);
            }
        }
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
            'controller'     => $this->getIdentifier()->package,
            'request'        => 'lib:dispatcher.request',
            'response'       => 'lib:dispatcher.response',
            'authenticators' => array()
        ));

        parent::_initialize($config);
    }

    /**
     * Get the request object
     *
     * @throws  UnexpectedValueException    If the request doesn't implement the KDispatcherRequestInterface
     * @return KDispatcherRequest
     */
    public function getRequest()
    {
        if(!$this->_request instanceof KDispatcherRequestInterface)
        {
            $this->_request = $this->getObject($this->_request);

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
     * @throws  UnexpectedValueException    If the response doesn't implement the KDispatcherResponseInterface
     * @return KDispatcherResponse
     */
    public function getResponse()
    {
        if(!$this->_response instanceof KDispatcherResponseInterface)
        {
            $this->_response = $this->getObject($this->_response, array(
                'request' => $this->getRequest(),
                'user'    => $this->getUser(),
            ));

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
     * Method to get a controller object
     *
     * @throws  UnexpectedValueException    If the controller doesn't implement the ControllerInterface
     * @return  KControllerAbstract
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
                'request'    => $this->getRequest(),
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
     * @param   mixed   $controller An object that implements KControllerInterface, KObjectIdentifier object
     *                              or valid identifier string
     * @param  array  $config  An optional associative array of configuration options
     * @return	KDispatcherAbstract
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

                $identifier         = $this->getIdentifier()->toArray();
                $identifier['path'] = array('controller');
                $identifier['name'] = $controller;

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
     * Attach an authenticator
     *
     * @param  mixed $authenticator An object that implements KDispatcherAuthenticatorInterface, an KObjectIdentifier
     *                              or valid identifier string
     * @param  array  $config  An optional associative array of configuration options
     * @return KDispatcherAbstract
     */
    public function addAuthenticator($authenticator, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($authenticator) && strpos($authenticator, '.') === false)
        {
            $identifier = $this->getIdentifier()->toArray();
            $identifier['path'] = array('dispatcher', 'authenticator');
            $identifier['name'] = $authenticator;

            $identifier = $this->getIdentifier($identifier);
        }
        else $identifier = $this->getIdentifier($authenticator);

        if (!isset($this->__authenticators[(string)$identifier]))
        {
            if(!$authenticator instanceof KDispatcherAuthenticatorInterface) {
                $authenticator = $this->getObject($identifier, $config);
            }

            if (!($authenticator instanceof KDispatcherAuthenticatorInterface))
            {
                throw new UnexpectedValueException(
                    "Authenticator $identifier does not implement KDispatcherAuthenticatorInterface"
                );
            }

            $this->addBehavior($authenticator);

            //Store the authenticator to allow for named lookups
            $this->__authenticators[(string)$identifier] = $authenticator;
        }

        return $this;
    }

    /**
     * Gets the authenticators
     *
     * @return array An array of authenticators
     */
    public function getAuthenticators()
    {
        return $this->__authenticators;
    }

    /**
     * Forward the request
     *
     * Forward to another dispatcher internally. Method makes an internal sub-request, calling the specified
     * dispatcher and passing along the context.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @throws UnexpectedValueException    If the dispatcher doesn't implement the KDispatcherInterface
     */
    protected function _actionForward(KDispatcherContextInterface $context)
    {
        //Get the dispatcher identifier
        if(is_string($context->param) && strpos($context->param, '.') === false )
        {
            $identifier            = $this->getIdentifier()->toArray();
            $identifier['package'] = $context->param;
        }
        else $identifier = $this->getIdentifier($context->param);

        //Create the dispatcher
        $config = array(
            'request'    => $context->request,
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
     * @param KDispatcherContextInterface $context  A dispatcher context object
     * @return  mixed
     */
    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        //Send the response
        $this->send($context);
    }

    /**
     * Render an exception
     *
     * @throws InvalidArgumentException If the action parameter is not an instance of Exception
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    protected function _actionFail(KDispatcherContextInterface $context)
    {
        //Check an exception was passed
        if(!isset($context->param) && !$context->param instanceof KException)
        {
            throw new InvalidArgumentException(
                "Action parameter 'exception' [KException] is required"
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
        $message = KHttpResponse::$status_messages[$code];

        //Set the response status
        $context->response->setStatus($code , $message);

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
    }
}
