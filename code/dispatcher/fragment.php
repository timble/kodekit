<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Fragment Dispatcher
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class KDispatcherFragment extends KDispatcherAbstract implements KObjectInstantiable, KObjectMultiton
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config	An optional ObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Resolve the request
        $this->addCommandCallback('before.include', '_resolveRequest');
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
            'dispatched'        => false,
            'controller'        => '',
            'controller_action' => 'render',
            'behaviors'         => array('localizable'),
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton
     *
     * @param 	KObjectConfig            $config	  A ObjectConfig object with configuration options
     * @param 	KObjectManagerInterface	$manager  A ObjectInterface object
     * @return  KDispatcherInterface
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        //Add the object alias to allow easy access to the singleton
        $manager->registerAlias($config->object_identifier, 'dispatcher.fragment');

        //Merge alias configuration into the identifier
        $config->append($manager->getIdentifier('dispatcher.fragment')->getConfig());

        //Instantiate the class
        $instance  = new static($config);

        return $instance;
    }

    /**
     * Get the request object
     *
     * @throws	UnexpectedValueException	If the request doesn't implement the DispatcherRequestInterface
     * @return KDispatcherRequest
     */
    public function getRequest()
    {
        if(!$this->_request instanceof KDispatcherRequestInterface) {
            $this->_request = clone $this->getObject('dispatcher.request');
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
        if(!$this->_response instanceof KDispatcherResponseInterface) {
            $this->_response = clone $this->getObject('dispatcher.response', array(
                'request' => $this->getRequest(),
                'user'    => $this->getUser()
            ));
        }

        return $this->_response;
    }

    /**
     * Resolve the request
     *
     * @param KDispatcherContextInterface $context A dispatcher context object
     */
    protected function _resolveRequest(KDispatcherContextInterface $context)
    {
        if($controller = KObjectConfig::unbox($context->param))
        {
            $url = $this->getObject('lib:http.url', array('url' => $controller));

            //Set the request query
            $context->request->query->clear()->add($url->getQuery(true));

            //Set the controller
            $identifier = $url->toString(KHttpUrl::BASE);
            $identifier = $this->getIdentifier($identifier);

            $this->setController($identifier);
        }

        parent::_resolveRequest($context);
    }

    /**
     * Include the request
     *
     * Dispatch to a controller internally or forward to another component and include the result by returning it.
     * Function makes an internal sub-request, based on the information in the request and passing along the context
     * and will return the result.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return	mixed
     */
    protected function _actionInclude(KDispatcherContextInterface $context)
    {
        return $this->_actionDispatch($context);
    }

    /**
     * Send the response
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return mixed
     */
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        return $context->result;
    }
}
