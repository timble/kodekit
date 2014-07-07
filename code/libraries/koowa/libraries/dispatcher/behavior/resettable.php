<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Resettable Dispatcher Behavior - Post, Redirect, Get
 *
 * When a browser sends a POST request (e.g. after submitting a form), the browser will try to protect them from sending
 * the POST again, breaking the back button, causing browser warnings and pop-ups, and sometimes re-posting the form.
 *
 * Instead, when receiving a none AJAX POST request reset the browser by redirecting it through a GET request.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class KDispatcherBehaviorResettable extends KControllerBehaviorAbstract
{
    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer   = $this->getMixer();
        $request = $mixer->getRequest();

        if(!$request->isSafe() && !$request->isAjax() && $request->getFormat() == 'html') {
            return true;
        }

        return false;
    }

    /**
	 * Force a GET after POST using the referrer
     *
     * Redirect if the controller has a returned a 2xx status code.
     * If a base64 encoded _referrer property exists in the request payload, it is used instead of the referrer.
	 *
	 * @param 	KDispatcherContextInterface $context The active command context
	 * @return 	void
	 */
	protected function _beforeSend(KDispatcherContextInterface $context)
	{
        $response = $context->response;
        $request  = $context->request;

        if($response->isSuccess())
        {
            $redirect = $request->getReferrer();

            if ($referrer = $context->getRequest()->getData()->get('_referrer', 'base64'))
            {
                $referrer = base64_decode($referrer);

                if ($this->getObject('lib:filter.internalurl')->validate($referrer)) {
                    $redirect = $referrer;
                }
            }

            $response->setRedirect($redirect);
        }
	}
}