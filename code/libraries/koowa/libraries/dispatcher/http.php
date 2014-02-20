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
class KDispatcherHttp extends KDispatcherAbstract implements KObjectMultiton
{
    /**
     * The limit information
     *
     * @var	array
     */
    protected $_limit;

    /**
	 * Constructor.
	 *
	 * @param KObjectConfig $config	An optional KObjectConfig object with configuration options.
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        //Set the limit
        $this->_limit = $config->limit;

        //Authenticate none safe requests
        $this->addCommandCallback('before.post'  , '_authenticateRequest');
        $this->addCommandCallback('before.put'   , '_authenticateRequest');
        $this->addCommandCallback('before.delete', '_authenticateRequest');

        //Sign GET request with a cookie token
        $this->addCommandCallback('after.get' , '_signResponse');
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	KObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
    	$config->append(array(
            'behaviors'  => array('resettable'),
            'limit'      => array('default' => 100)
         ));

        parent::_initialize($config);
    }

    /**
     * Check the request token to prevent CSRF exploits
     *
     * Method will always perform a referrer check and a token cookie token check if the user is not authentic or a
     * session token check if the user is authentic. If any of the checks fail a forbidden exception is thrown.
     *
     * @param KDispatcherContextInterface $context A dispatcher context object
     * @throws KControllerExceptionRequestInvalid           If the request referrer is not valid
     * @throws KControllerExceptionRequestForbidden         If the cookie token is not valid
     * @throws KControllerExceptionRequestNotAuthenticated  If the session token is not valid
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    protected function _authenticateRequest(KDispatcherContextInterface $context)
    {
        $request = $context->request;
        $user    = $context->user;

        if(!$user->isAuthentic())
        {
            //Check referrer
            if(!$request->getReferrer()) {
                throw new KControllerExceptionRequestInvalid('Invalid Request Referrer');
            }

            //Check cookie token
            if($request->getToken() !== $request->cookies->get('_token', 'sha1')) {
                throw new KControllerExceptionRequestNotAuthenticated('Invalid Cookie Token');
            }
        }
        else
        {
            //Check session token
            if( $request->getToken() !== $user->getSession()->getToken()) {
                throw new KControllerExceptionRequestForbidden('Invalid Session Token');
            }
        }

        return true;
    }

    /**
     * Sign the response with a token
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    protected function _signResponse(KDispatcherContextInterface $context)
    {
        if(!$context->response->isError())
        {
            $token = $context->user->getSession()->getToken();

            $context->response->headers->addCookie($this->getObject('lib:http.cookie', array(
                'name'   => '_token',
                'value'  => $token,
                'path'   => $context->request->getBaseUrl()->getPath() ?: '/'
            )));

            $context->response->headers->set('X-Token', $token);
        }
    }

    /**
     * Dispatch the request
     *
     * Dispatch to a controller internally. Functions makes an internal sub-request, based on the information in
     * the request and passing along the context.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @throws  KDispatcherExceptionMethodNotAllowed  If the method is not allowed on the resource.
     * @return	mixed
     */
	protected function _actionDispatch(KDispatcherContextInterface $context)
	{
        //Execute the component method
        $method = strtolower($context->request->getMethod());
        $this->execute($method, $context);

        return parent::_actionDispatch($context);
	}

    /**
     * Redirect
     *
     * Redirect to a URL externally. Method performs a 301 (permanent) redirect. Method should be used to immediately
     * redirect the dispatcher to another URL after a GET request.
     *
     * @param KDispatcherContextInterface $context A dispatcher context object
     * @return bool
     */
    protected function _actionRedirect(KDispatcherContextInterface $context)
    {
        $url = $context->param;

        $context->response->setStatus(KDispatcherResponse::MOVED_PERMANENTLY);
        $context->response->setRedirect($url);
        $this->send();

        return false;
    }

    /**
     * Get method
     *
     * This function translates a GET request into a render action.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return KDatabaseRowInterface|KDatabaseRowsetInterface A row(set) object containing the modified data
     */
    protected function _actionGet(KDispatcherContextInterface $context)
    {
        $controller = $this->getController();

        if($controller instanceof KControllerModellable)
        {
            $controller->getModel()->getState()->setProperty('limit', 'default', $this->_limit->default);

            if(!$controller->getModel()->getState()->isUnique())
            {
                $limit = $this->getRequest()->query->get('limit', 'int');

                //Allow a zero limit, set to default is limit is not set
                if(empty($limit)) {
                    $limit = $this->_limit->default;
                }

                if ($this->_limit->max && $limit > $this->_limit->max) {
                    $limit = $this->_limit->max;
                }

                $this->getRequest()->query->limit = $limit;
                $controller->getModel()->getState()->limit = $limit;
            }
        }

        return $controller->execute('render', $context);
    }

    /**
     * Post method
     *
     * This function translated a POST request action into an edit or add action. If the model state is unique a edit
     * action will be executed, if not unique an add action will be executed.
     *
     * If an _action parameter exists in the request data it will be used instead. If no action can be found an bad
     * request exception will be thrown.
     *
     * @param   KDispatcherContextInterface $context  A dispatcher context object
     * @throws  KDispatcherExceptionMethodNotAllowed  The action specified in the request is not allowed for the
     *          entity identified by the Request-URI. The response MUST include an Allow header containing a list of
     *          valid actions for the requested entity.
     * @throws  KControllerExceptionRequestInvalid    The action could not be found based on the info in the request.
     * @return 	KDatabaseRowInterface|KDatabaseRowsetInterface	A row(set) object containing the modified data
     */
    protected function _actionPost(KDispatcherContextInterface $context)
    {
        $result     = false;
        $action     = null;
        $controller = $this->getController();

        if($controller instanceof KControllerModellable)
        {
            //Get the action from the request data
            if($context->request->data->has('_action'))
            {
                $action = strtolower($context->request->data->get('_action', 'alnum'));

                if(in_array($action, array('browse', 'read', 'render'))) {
                    throw new KDispatcherExceptionMethodNotAllowed('Action: '.$action.' not allowed');
                }
            }
            else
            {
                //Determine the action based on the model state
                if($controller instanceof KControllerModellable) {
                    $action = $controller->getModel()->getState()->isUnique() ? 'edit' : 'add';
                }
            }

            //Throw exception if no action could be determined from the request
            if(!$action) {
                throw new KControllerExceptionRequestInvalid('Action not found');
            }

            $result = $controller->execute($action, $context);
        }
        else throw new KDispatcherExceptionMethodNotAllowed('Method POST not allowed');

        return $result;
    }

    /**
     * Put method
     *
     * This function translates a PUT request into an edit or add action. Only if the model state is unique and the item
     * exists an edit action will be executed, if the entity does not exist and the state is unique an add action will
     * be executed.
     *
     * If the entity already exists it will be completely replaced based on the data available in the request.
     *
     * @param   KDispatcherContextInterface $context	A dispatcher context object
     * @throws  KControllerExceptionRequestInvalid 	If the model state is not unique
     * @return 	KDatabaseRowInterface|KDatabaseRowsetInterface	    A row(set) object containing the modified data
     */
    protected function _actionPut(KDispatcherContextInterface $context)
    {
        $action     = null;
        $controller = $this->getController();

        if($controller instanceof KControllerModellable)
        {
            if($controller->getModel()->getState()->isUnique())
            {
                $action = 'add';
                $entity = $controller->getModel()->getItem();

                if(!$entity->isNew())
                {
                    //Reset the row data
                    $entity->reset();
                    $action = 'edit';
                }

                //Set the row data based on the unique state information
                $state = $controller->getModel()->getState()->getValues(true);
                $entity->setData($state);
            }
            else throw new KControllerExceptionRequestInvalid('Resource not found');

            //Throw exception if no action could be determined from the request
            if(!$action) {
                throw new KControllerExceptionRequestInvalid('Resource not found');
            }

            $result = $controller->execute($action, $context);
        }
        else throw new KDispatcherExceptionMethodNotAllowed('Method PUT not allowed');

        return $result;
    }

    /**
     * Delete method
     *
     * This function translates a DELETE request into a delete action.
     *
     * @param   KDispatcherContextInterface $context	A dispatcher context object
     * @return 	KDatabaseRowInterface|KDatabaseRowsetInterface	A row(set) object containing the modified data
     */
    protected function _actionDelete(KDispatcherContextInterface $context)
    {
        $result     = false;
        $controller = $this->getController();

        if($controller instanceof KControllerModellable) {
            $result = $controller->execute('delete', $context);
        } else {
            throw new KDispatcherExceptionMethodNotAllowed('Method DELETE not allowed');
        }

        return $result;
    }

    /**
     * Options method
     *
     * @param   KDispatcherContextInterface $context	A dispatcher context object
     * @return  string  The allowed actions; e.g., `GET, POST [add, edit, cancel, save], PUT, DELETE`
     */
    protected function _actionOptions(KDispatcherContextInterface $context)
    {
        $methods = array();

        //Retrieve HTTP methods allowed by the dispatcher
        $actions = array_diff($this->getActions(), array('dispatch'));

        foreach($actions as $action)
        {
            if($this->canExecute($action)) {
                $methods[$action] = $action;
            }
        }

        //Retrieve POST actions allowed by the controller
        if(in_array('post', $methods))
        {
            $actions = array_diff($this->getController()->getActions(), array('browse', 'read', 'render'));

            foreach($actions as $key => $action)
            {
                if(!$this->getController()->canExecute($action)) {
                    unset($actions[$key]);
                }
            }

            sort($actions);

            $methods['post'] = array_diff($actions, $methods);
        }

        //Render to string
        $result = '';
        foreach($methods as $method => $actions)
        {
            $result .= strtoupper($method). ' ';
            if(is_array($actions) && !empty($actions)) {
                $result .= '['.implode(', ', $actions).'] ';
            }
        }

        $context->response->headers->set('Allow', $result);
    }

    /**
     * Send the response to the client
     *
     * - Set the affected entities in the payload for none-SAFE requests that return a successful response. Make an
     * exception for 204 No Content responses which should not return a response body.
     *
     * - Add an Allow header to the response if the status code is 405 METHOD NOT ALLOWED.
     *
     * {@inheritdoc}
     */
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        if (!$request->isSafe())
        {
            if ($response->isSuccess())
            {
                //Render the controller and set the result in the response body
                if($response->getStatusCode() !== KHttpResponse::NO_CONTENT) {
                    $context->result = $this->getController()->execute('render', $context);
                }
            }
            else
            {
                //Add an Allow header to the reponse
                if($response->getStatusCode() === KHttpResponse::METHOD_NOT_ALLOWED) {
                    $this->_actionOptions($context);
                }
            }
        }

        parent::_actionSend($context);
    }
}