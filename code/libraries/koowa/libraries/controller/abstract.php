<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Controller
 *
 * Note: Concrete controllers must have a singular name
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerAbstract extends KObject implements KControllerInterface, KCommandCallbackDelegate
{
    /**
     * The class actions
     *
     * @var array
     */
    protected $_actions = array();

    /**
     * Response object or identifier
     *
     * @var	string|object
     */
    protected $_response;

    /**
     * Request object or identifier
     *
     * @var	string|object
     */
    protected $_request;

    /**
     * User object or identifier
     *
     * @var	string|object
     */
    protected $_user;

    /**
     * Has the controller been dispatched
     *
     * @var boolean
     */
    protected $_dispatched;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options.
     */
    public function __construct( KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the dispatched state
        $this->_dispatched = $config->dispatched;

        // Set the model identifier
        $this->_request = $config->request;

        // Set the view identifier
        $this->_response = $config->response;

        // Set the user identifier
        $this->_user = $config->user;

        // Mixin the behavior (and command) interface
        $this->mixin('lib:behavior.mixin', $config);

        // Mixin the event interface
        $this->mixin('lib:event.mixin', $config);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'command_chain'     => 'lib:command.chain',
            'command_handlers'  => array('lib:command.handler.event'),
            'dispatched'		=> false,
            'request'           => 'lib:controller.request',
            'response'          => 'lib:controller.response',
            'user'              => 'lib:user',
            'behaviors'         => array('permissible'),
        ));

        parent::_initialize($config);
    }

    /**
     * Has the controller been dispatched
     *
     * @return  boolean	Returns true if the controller has been dispatched
     */
    public function isDispatched()
    {
        return $this->_dispatched;
    }

    /**
     * Execute an action by triggering a method in the derived class.
     *
     * @param   string                      $action  The action to execute
     * @param   KControllerContextInterface $context A command context object
     * @throws  Exception
     * @throws  BadMethodCallException
     * @return  mixed|bool The value returned by the called method, false in error case.
     */
    public function execute($action, KControllerContextInterface $context)
    {
        $action  = strtolower($action);

        //Set the context subject
        $context_subject = $context->getSubject();
        $context->setSubject($this);

        //Set the context action
        $context_action = $context->getAction();
        $context->setAction($action);

        //Execute the action
        if($this->invokeCommand('before.'.$action, $context) !== false)
        {
            $method = '_action' . ucfirst($action);

            if (!method_exists($this, $method))
            {
                if (isset($this->_mixed_methods[$action]))
                {
                    $context->setName('action.' . $action);
                    $context->result = $this->_mixed_methods[$action]->execute($context, $this->getCommandChain());
                }
                else
                {
                    throw new KControllerExceptionActionNotImplemented(
                        "Can't execute '$action', method: '$method' does not exist"
                    );
                }
            }
            else  $context->result = $this->$method($context);

            $this->invokeCommand('after.'.$action, $context);
        }

        //Reset the context
        $context->setSubject($context_subject);
        $context->setAction($context_action);

        return $context->result;
    }

    /**
     * Invoke a command handler
     *
     * @param string             $method    The name of the method to be executed
     * @param KCommandInterface  $command   The command
     * @return mixed Return the result of the handler.
     */
    public function invokeCommandCallback($method, KCommandInterface $command)
    {
        return $this->$method($command);
    }

    /**
     * Mixin an object
     *
     * When using mixin(), the calling object inherits the methods of the mixed in objects, in a LIFO order.
     *
     * @@param   mixed  $mixin  An object that implements KObjectMixinInterface, KObjectIdentifier object
     *                          or valid identifier string
     * @param    array $config  An optional associative array of configuration options
     * @return  KObject
     */
    public function mixin($mixin, $config = array())
    {
        if ($mixin instanceof KControllerBehaviorAbstract)
        {
            $actions = $this->getActions();

            foreach ($mixin->getMethods() as $method)
            {
                if (substr($method, 0, 7) == '_action') {
                    $actions[] = strtolower(substr($method, 7));
                }
            }

            $this->_actions = array_unique($actions);
        }

        return parent::mixin($mixin, $config);
    }

    /**
     * Gets the available actions in the controller.
     *
     * @return  array Array[i] of action names.
     */
    public function getActions()
    {
        if (!$this->_actions)
        {
            $this->_actions = array();

            foreach ($this->getMethods() as $method)
            {
                if (substr($method, 0, 7) == '_action') {
                    $this->_actions[] = strtolower(substr($method, 7));
                }
            }

            $this->_actions = array_unique($this->_actions);
        }

        return $this->_actions;
    }

    /**
     * Set the request object
     *
     * @param KControllerRequestInterface $request A request object
     * @return KControllerAbstract
     */
    public function setRequest(KControllerRequestInterface $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get the request object
     *
     * @throws UnexpectedValueException	If the request doesn't implement the ControllerRequestInterface
     * @return KControllerRequestInterface
     */
    public function getRequest()
    {
        if(!$this->_request instanceof KControllerRequestInterface)
        {
            $this->_request = $this->getObject($this->_request,  array(
                'url'  => $this->getIdentifier(),
            ));

            if(!$this->_request instanceof KControllerRequestInterface)
            {
                throw new UnexpectedValueException(
                    'Request: '.get_class($this->_request).' does not implement KControllerRequestInterface'
                );
            }
        }

        return $this->_request;
    }

    /**
     * Set the response object
     *
     * @param KControllerResponseInterface $response A response object
     * @return KControllerAbstract
     */
    public function setResponse(KControllerResponseInterface $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Get the response object
     *
     * @throws	UnexpectedValueException	If the response doesn't implement the ControllerResponseInterface
     * @return KControllerResponseInterface
     */
    public function getResponse()
    {
        if(!$this->_response instanceof KControllerResponseInterface)
        {
            $this->_response = $this->getObject($this->_response, array(
                'request' => $this->getRequest(),
                'user'    => $this->getUser(),
            ));

            if(!$this->_response instanceof KControllerResponseInterface)
            {
                throw new UnexpectedValueException(
                    'Response: '.get_class($this->_response).' does not implement KControllerResponseInterface'
                );
            }
        }

        return $this->_response;
    }

    /**
     * Set the user object
     *
     * @param KUserInterface $user A request object
     * @return KControllerAbstract
     */
    public function setUser(KUserInterface $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * Get the user object
     *
     * @throws UnexpectedValueException	If the user doesn't implement the KUserInterface
     * @return KUserInterface
     */
    public function getUser()
    {
        if(!$this->_user instanceof KUserInterface)
        {
            $this->_user = $this->getObject($this->_user, array(
                'request' => $this->getRequest(),
            ));

            if(!$this->_user instanceof KUserInterface)
            {
                throw new UnexpectedValueException(
                    'User: '.get_class($this->_user).' does not implement KUserInterface'
                );
            }
        }

        return $this->_user;
    }

    /**
     * Get the controller context
     *
     * @return  KControllerContext
     */
    public function getContext()
    {
        $context = new KControllerContext();
        $context->setSubject($this);
        $context->setRequest($this->getRequest());
        $context->setResponse($this->getResponse());
        $context->setUser($this->getUser());

        return $context;
    }

    /**
     * Execute a controller action by it's name.
     *
     * Function is also capable of checking is a behavior has been mixed successfully using is[Behavior] function. If
     * the behavior exists the function will return TRUE, otherwise FALSE.
     *
     * @param  string  $method Method name
     * @param  array   $args   Array containing all the arguments for the original call
     * @return mixed
     * @see execute()
     */
    public function __call($method, $args)
    {
        //Handle action alias method
        if(in_array($method, $this->getActions()))
        {
            //Get the data
            $data = !empty($args) ? $args[0] : array();

            //Create a context object
            if(!($data instanceof KCommandInterface))
            {
                $context = $this->getContext();

                //Store the parameters in the context
                $context->param = $data;

                //Force the result to false before executing
                $context->result = false;
            }
            else $context = $data;

            //Execute the action
            return $this->execute($method, $context);
        }

        if (!isset($this->_mixed_methods[$method]))
        {
            //Check if a behavior is mixed
            $parts = KStringInflector::explode($method);

            if ($parts[0] == 'is' && isset($parts[1])) {
                return false;
            }
        }

        return parent::__call($method, $args);
    }
}
