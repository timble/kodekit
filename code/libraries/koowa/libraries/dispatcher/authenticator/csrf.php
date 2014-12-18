<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Csrf Dispatcher Authenticator
 *
 * @link http://www.adambarth.com/papers/2008/barth-jackson-mitchell-b.pdf
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher\Authenticator
 */
class KDispatcherAuthenticatorCsrf extends KDispatcherAuthenticatorAbstract
{
    /**
     * The CSRF token
     *
     * @var string
     */
    private $__token;

    /**
     * Constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.post', 'authenticateRequest');
        $this->addCommandCallback('after.get'  , 'signResponse');
    }

    /**
     * Return the CSRF request token
     *
     * @return  string  The CSRF token or NULL if no token could be found
     */
    public function getCsrfToken()
    {
        if(!isset($this->__token))
        {
            $token   = false;
            $request = $this->getObject('request');

            if($request->headers->has('X-XSRF-Token')) {
                $token = $request->headers->get('X-XSRF-Token');
            }

            if($request->headers->has('X-CSRF-Token')) {
                $token = $request->headers->get('X-CSRF-Token');
            }

            if($request->data->has('csrf_token')) {
                $token = $request->data->get('csrf_token', 'sha1');
            }

            $this->__token = $token;
        }

        return $this->__token;
    }

    /**
     * Verify the request to prevent CSRF exploits
     *
     * Method will always perform a referrer check and a cookie token check if the user is not authentic and
     * additionally a session token check if the user is authentic.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     *
     * @throws KControllerExceptionRequestInvalid      If the request referrer is not valid
     * @throws KControllerExceptionRequestForbidden    If the cookie token is not valid
     * @throws KControllerExceptionRequestNotAuthenticated If the session token is not valid
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    public function authenticateRequest(KDispatcherContextInterface $context)
    {
        $request = $context->request;
        $user    = $context->user;

        //Check referrer
        if(!$request->getReferrer()) {
            throw new KControllerExceptionRequestInvalid('Request Referrer Not Found');
        }

        //Check csrf token
        if(!$this->getCsrfToken()) {
            throw new KControllerExceptionRequestNotAuthenticated('Csrf Token Not Found');
        }

        //Check cookie token
        if($this->getCsrfToken() !== $request->cookies->get('csrf_token', 'sha1')) {
            throw new KControllerExceptionRequestNotAuthenticated('Invalid Cookie Token');
        }

        if($user->isAuthentic())
        {
            //Check session token
            if( $this->getCsrfToken() !== $user->getSession()->getToken()) {
                throw new KControllerExceptionRequestForbidden('Invalid Session Token');
            }
        }

        return true;
    }

    /**
     * Sign the response with a session token
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    public function signResponse(KDispatcherContextInterface $context)
    {
        if(!$context->response->isError())
        {
            $token = $context->user->getSession()->getToken();

            $context->response->headers->addCookie($this->getObject('lib:http.cookie', array(
                'name'   => 'csrf_token',
                'value'  => $token,
                'path'   => $context->request->getBaseUrl()->getPath(),
            )));

            $context->response->headers->set('X-CSRF-Token', $token);
        }
    }
}