<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaDispatcherHttp extends KDispatcherHttp implements KObjectInstantiable
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config	An optional KObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Force the controller to the information found in the request
        if($config->request->view) {
            $this->_controller = $config->request->view;
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'limit'     => array('default' => JFactory::getApplication()->getCfg('list_limit')),
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton
     *
     * @param  KObjectConfigInterface  $config  Configuration options
     * @param  KObjectManagerInterface $manager	A KObjectManagerInterface object
     * @return KDispatcherDefault
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        // Check if an instance with this identifier already exists or not
        if (!$manager->isRegistered($config->object_identifier))
        {
            //Create the singleton
            $classname = $config->object_identifier->classname;
            $instance  = new $classname($config);
            $manager->setObject($config->object_identifier, $instance);

            //Add the factory map to allow easy access to the singleton
            $manager->registerAlias('dispatcher', $config->object_identifier);
        }

        return $manager->getObject($config->object_identifier);
    }

    /**
     * Check the request token to prevent CSRF exploits
     *
     * Method will always perform a cookie token check. If a user session is active a session token check
     * will also be done. If any of the checks fail an forbidden exception  being thrown.
     *
     * @param   KDispatcherContext $context The command context
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    public function authenticateRequestK(DispatcherContextInterfacet $context)
    {
        //Check cookie token
        if(KRequest::token() !== KRequest::get('cookie._token', 'md5')) {
            throw new KControllerExceptionForbidden('Invalid Cookie Token');
        }

        //Check session token
        if(!JFactory::getUser()->guest)
        {
            if( KRequest::token() !== JSession::getFormToken()) {
                throw new KControllerExceptionForbidden('Invalid Session Token');
            }
        }

        return true;
    }

    /**
     * Sign the response with a token
     *
     * @param KDispatcherContext $context	A dispatcher context object
     */
    public function signResponse(KDispatcherContextInterface $context)
    {
        //if(!$context->response->isError())
        //{
            $token = JSession::getFormToken();

            setcookie('_token', $token, 0, KRequest::base().'/');
            header('X-Token : '.$token);
        //}
    }

    /**
     * Get the request object
     *
     * Joomla 3 Compatibility. Re-run the routing and add returned keys to the $_GET request. Joomla 3 sets the results
     * of the router in $_REQUEST and not in $_GET (which is wrong).
     *
     * @throws  UnexpectedValueException	If the request doesn't implement the KDispatcherRequestInterface
     * @return ComKoowaDispatcherHttp
     */
    public function getRequest()
    {
        $app = JFactory::getApplication();
        if ($app->isSite() && $app->getCfg('sef'))
        {
            $uri = clone JURI::getInstance();

            $router = JFactory::getApplication()->getRouter();
            $result = $router->parse($uri);

            foreach ($result as $key => $value)
            {
                if (!KRequest::has('get.'.$key)) {
                    KRequest::set('get.'.$key, $value);
                }
            }
        }

        return parent::getRequest();
    }

    /*
     * Overridden for translating 'limitstart' to 'offset' for compatibility with Joomla
     */
    public function setRequest(array $request)
    {
        if (isset($request['limitstart'])) {
            $request['offset'] = $request['limitstart'];
        }

        return parent::setRequest($request);
    }

    /**
     * Dispatch the controller and redirect
     *
     * This function divert the standard behavior and will redirect if no view information can be found in the request.
     *
     * @param KDispatcherContextInterface $context A command context object
     * @return  ComKoowaDispatcherHttp
     */
    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        //Redirect if no view information can be found in the request
        if(!KRequest::has('get.view'))
        {
            $url = clone(KRequest::url());
            $url->query['view'] = $this->getController()->getView()->getName();

            $this->redirect($url);
        }

        return parent::_actionDispatch($context);
    }

    /**
     * Redirect
     *
     * Redirect to a URL externally. Method performs a 301 (permanent) redirect. Method should be used to immediately
     * redirect the dispatcher to another URL after a GET request.
     *
     * @param KDispatcherContextInterface $context   A command context object
     */
    protected function _actionRedirect(KDispatcherContextInterface $context)
    {
        $url = $context->param;
        JFactory::getApplication()->redirect($url);

        return false;
    }

    /**
     * Push the controller data into the document
     *
     * This function divert the standard behavior and will push specific controller data into the document
     *
     * @param   KDispatcherContextInterface	$context A command context object
     * @return  ComKoowaDispatcherHttp
     */
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        $view = $this->getController()->getView();

        //Send the mimetype
        JFactory::getDocument()->setMimeEncoding($view->mimetype);

        //Disabled the application menubar
        if($this->getIdentifier()->application === 'admin')
        {
            if($this->getController()->isEditable() && KStringInflector::isSingular($view->getName())) {
                KRequest::set('get.hidemainmenu', 1);
            }
        }

        //Redirect the request
        if (KRequest::method() != 'GET' && KRequest::type() == 'HTTP')
        {
            if($redirect = $this->getController()->getRedirect())
            {
                JFactory::getApplication()
                    ->redirect($redirect['url'], $redirect['message'], $redirect['type']);
            }
        }

        return parent::_actionSend($context);
    }
}
