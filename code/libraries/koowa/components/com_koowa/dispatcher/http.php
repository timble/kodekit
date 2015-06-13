<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Dispatcher
 */
class ComKoowaDispatcherHttp extends KDispatcherHttp
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config	An optional KObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Render an exception before sending the response
        $this->addCommandCallback('before.fail', '_renderError');
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
            'response'          => 'com:koowa.dispatcher.response',
            'request'           => 'com:koowa.dispatcher.request',
            'event_subscribers' => array('unauthorized'),
            'user'              => 'com:koowa.user',
            'limit'             => array(
                'default' => JFactory::getApplication()->getCfg('list_limit'),
                'max'     => 100
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Render an exception
     *
     * @throws InvalidArgumentException If the action parameter is not an instance of Exception
     * @param KDispatcherContextInterface $context  A dispatcher context object
     * @return boolean|null
     */
    protected function _renderError(KDispatcherContextInterface $context)
    {
        $request   = $context->request;
        $response  = $context->response;

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

        //Make sure the output buffers are cleared
        $level = ob_get_level();
        while($level > 0) {
            ob_end_clean();
            $level--;
        }

        //Render the error
        if(!JDEBUG && $request->getFormat() == 'html')
        {
            //If the error code does not correspond to a status message, use 500
            $code = $exception->getCode();
            if(!isset(KHttpResponse::$status_messages[$code])) {
                $code = '500';
            }

            if(ini_get('display_errors')) {
                $message = $exception->getMessage();
            } else {
                $message = KHttpResponse::$status_messages[$code];
            }

            $message = $this->getObject('translator')->translate($message);

            if (version_compare(JVERSION, '3.0', '>='))
            {
                $class = get_class($exception);
                $error = new $class($message, $exception->getCode());
                JErrorPage::render($error);

                JFactory::getApplication()->close(0);
            }
            else JError::raiseError($exception->getCode(), $message);

            return false;
        }
        else
        {
            //Render the exception if debug mode is enabled or if we are returning json
            if(in_array($request->getFormat(), array('json', 'html')))
            {
                $config = array(
                    'request'  => $request,
                    'response' => $response
                );

                $this->getObject('com:koowa.controller.error',  $config)
                    ->layout('default')
                    ->render($exception);

                //Do not pass response back to Joomla
                $context->request->query->set('tmpl', 'koowa');
            }
        }
    }

    /**
     * Dispatch the request
     *
     * Dispatch to a controller internally. Functions makes an internal sub-request, based on the information in
     * the request and passing along the context.
     *
     * @param KDispatcherContextInterface $context  A dispatcher context object
     * @throws  KDispatcherExceptionMethodNotAllowed  If the method is not allowed on the resource.
     * @return  mixed
     */
    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        //Set the response messages
        $context->response->setMessages($this->getUser()->getSession()->getContainer('message')->all());

        return parent::_actionDispatch($context);
    }
}
