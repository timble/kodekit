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
        if($this->getRequest()->query->has('view')) {
            $this->_controller = $this->getRequest()->query->get('view', 'cmd');
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
            'request'  => 'com:koowa.dispatcher.request',
            'limit'    => array('default' => JFactory::getApplication()->getCfg('list_limit')),
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
     * @param   KDispatcherContextInterface $context The command context
     * @throws KControllerExceptionForbidden
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    public function authenticateRequest(KDispatcherContextInterface $context)
    {
        $request = $context->request;

        //Check cookie token
        if($request->getToken() !== $request->cookies->get('_token', 'md5')) {
            throw new KControllerExceptionForbidden('Invalid Cookie Token');
        }

        //Check session token
        if(!JFactory::getUser()->guest)
        {
            if($request->getToken() !== JSession::getFormToken()) {
                throw new KControllerExceptionForbidden('Invalid Session Token or Session Time-out');
            }
        }

        return true;
    }

    /**
     * Sign the response with a token
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    public function signResponse(KDispatcherContextInterface $context)
    {
        if(!$context->response->isError())
        {
            $token = JSession::getFormToken();

            setcookie('_token', $token, 0, KRequest::base().'/');
            header('X-Token : '.$token);
        }
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
        if(!$this->getRequest()->query->has('view'))
        {
            $url = clone($context->request->getUrl());
            $url->query['view'] = $this->getController()->getView()->getName();

            $this->redirect($url);
        }

        return parent::_actionDispatch($context);
    }

    /**
     * Push the controller data into the document
     *
     * This function will pass back to Joomla if the following conditions are met :
     *    - response content type is text/html
     *    - response is not a redirect
     *    - request is not an ajax request
     *
     * @param   KDispatcherContextInterface	$context A command context object
     * @return  ComKoowaDispatcherHttp
     */
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        //Redirect the request
        if (KRequest::method() != 'GET' && KRequest::type() == 'HTTP')
        {
            if($redirect = $this->getController()->getRedirect()) {
                JFactory::getApplication()->redirect($redirect['url'], $redirect['message'], $redirect['type']);
            }
        }

        //Only pass back to Joomla.
        if(!$context->response->isRedirect() && $context->response->getContentType() == 'text/html' && !$context->request->isAjax())
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

            return $context->response->getContent();
        }

        return parent::_actionSend($context);
    }
}
