<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Dispatcher Authenticator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class KDispatcherAuthenticatorToken extends KDispatcherAuthenticatorAbstract
{
    /**
     * Check the request token to prevent CSRF exploits
     *
     * Method will always perform a referrer check and a token cookie token check if the user is not authentic and
     * additionally a session token check if the user is authentic. If any of the checks fail a forbidden exception
     * is thrown.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     *
     * @throws KControllerExceptionRequestInvalid      If the request referrer is not valid
     * @throws KControllerExceptionRequestForbidden    If the cookie token is not valid
     * @throws KControllerExceptionRequestNotAuthenticated If the session token is not valid
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    protected function _beforePost(KDispatcherContextInterface $context)
    {
        $request = $context->request;
        $user    = $context->user;

        //Check referrer
        if(!$request->getReferrer()) {
            throw new KControllerExceptionRequestInvalid('Invalid Request Referrer');
        }

        //Check cookie token
        if($request->getToken() !== $request->cookies->get('_token', 'sha1')) {
            throw new KControllerExceptionRequestNotAuthenticated('Invalid Cookie Token');
        }

        if($user->isAuthentic())
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
    protected function _afterGet(KDispatcherContextInterface $context)
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
}