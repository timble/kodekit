<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Dispatcher Authenticator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
abstract class KDispatcherAuthenticatorAbstract extends KBehaviorAbstract implements KDispatcherAuthenticatorInterface
{
    /**
     * Authenticate the request
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return bool Returns TRUE if the request could be authenticated, FALSE otherwise.
     */
    public function authenticateRequest(KDispatcherContextInterface $context)
    {
        return false;
    }

    /**
     * Sign the response
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     * @return bool Returns TRUE if the response could be signed, FALSE otherwise.
     */
    public function signResponse(KDispatcherContextInterface $context)
    {
        return false;
    }

    /**
     * Get the methods that are available for mixin based
     *
     * @param  array           $exclude     An array of public methods to be exclude
     * @return array An array of methods
     */
    public function getMixableMethods($exclude = array())
    {
        $exclude += array('authenticateRequest', 'signResponse');
        return parent::getMixableMethods($exclude);
    }
}