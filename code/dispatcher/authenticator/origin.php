<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Csrf Dispatcher Authenticator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Dispatcher\Authenticator
 */
class DispatcherAuthenticatorOrigin extends DispatcherAuthenticatorAbstract
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
     * Verify the request to prevent CSRF exploits
     *
     * We first check if X-Requested-With header is present or not. If it is, the request is coming from an identified
     * origin as it's a non-safe CORS header and a form submit from a third party website wouldn't include the header.
     * If the browser cleared the request to hit our end after passing the CORS preflight request we deem the request
     * safe.
     *
     * See: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html#use-of-custom-request-headers
     *
     * If the header is not present (any other POST request like a normal form submit we check for `Origin` header with
     * a fallback to `Referer` header. If the origin (or referer) header is present and on our list of allowed origins
     * we deem the request safe.
     *
     * See: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html#verifying-origin-with-standard-headers
     *
     * @param DispatcherContext $context	A dispatcher context object
     * @throws ControllerExceptionRequestInvalid      If the request referrer is not valid
     * @throws ControllerExceptionRequestForbidden    If the cookie token is not valid
     * @throws ControllerExceptionRequestNotAuthenticated If the session token is not valid
     * @return void
     */
    public function authenticateRequest(DispatcherContext $context)
    {
        $request = $context->getRequest();

        if($this->isPost())
        {
            // Mere presence of the X-Requested-With header is a sign that the request is coming from an identified origin:
            if (!$request->headers->has('X-Requested-With'))
            {
                $origin = $request->headers->get('Origin');

                //No Origin, fallback to Referer
                if(!$origin) {
                    $origin = $request->headers->get('Referer');
                }

                //Don't not allow origin to be empty or null (possible in some cases)
                if(!empty($origin))
                {
                    $match  = false;
                    $origin = $this->getObject('lib:filter.url')->sanitize($origin);
                    $source = HttpUrl::fromString($origin)->getHost();

                    foreach($request->getOrigins() as $target)
                    {
                        // Check if the source matches the target
                        if($target == $source || '.'.$target === substr($source, -1 * (strlen($target)+1))) {
                            $match = true; break;
                        }
                    }

                    if(!$match) {
                        throw new ControllerExceptionRequestInvalid('Origin or referer not valid');
                    }
                }
                else throw new ControllerExceptionRequestInvalid('Origin or referer required');
            }
        }
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}