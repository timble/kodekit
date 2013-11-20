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

	    $this->registerCallback('after.dispatch', array($this, 'send'));
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
            'behaviors'  => array('permissible'),
            'controller' => $this->getIdentifier()->package,
            'request'	 => KRequest::get('get', 'string')
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
     * Get the controller context
     *
     * @return KDispatcherContext
     */
    public function getContext()
    {
        $context = new KDispatcherContext();

        $context->setSubject($this);
        $context->setRequest($this->getRequest());

        return $context;
    }

    /**
     * Forward the request
     *
     * Forward to another dispatcher internally. Method makes an internal sub-request, calling the specified
     * dispatcher and passing along the context.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @throws	\UnexpectedValueException	If the dispatcher doesn't implement the KDispatcherInterface
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

        $dispatcher = $this->getObject($identifier);

        if(!$dispatcher instanceof KDispatcherInterface)
        {
            throw new \UnexpectedValueException(
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
        //Headers
        if($context->headers)
        {
            foreach($context->headers as $name => $value) {
                header($name.' : '.$value);
            }
        }

        //Status
        if($context->status) {
            header('HTTP/1.1 '.$context->status.' '.KHttpResponse::$status_messages[$context->status]);
        }

        //Content
        if(is_string($context->result)) {
            return $context->result;
        }
    }
}
