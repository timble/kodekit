<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Resettable Dispatcher Behavior - Post, Redirect, Get
 *
 * When a client sends a POST request (e.g. after submitting a form), the browser will try to protect them from sending
 * the POST again, breaking the back button, causing browser warnings and pop-ups, and sometimes re-posting the form.
 *
 * Instead, when receiving a POST and when we are not responding with a 204 NO_CONTENT we reset the form by redirecting
 * the client through a GET request.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class KDispatcherBehaviorResettable extends KControllerBehaviorAbstract
{
    /**
     * Get an object handle
     *
     * Only attach this behavior for form (application/x-www-form-urlencoded) POST requests.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        $result = null;
        if($this->getRequest()->isPost() && $this->getRequest()->getContentType() == 'application/x-www-form-urlencoded') {
            $result = parent::getHandle();
        }

        return $result;
    }

    /**
	 * Force a GET after POST using the referrer
     *
     * Method will only set the redirect for none AJAX requests and only if the controller has a returned a 2xx status
     * code. In all other cases no redirect will be set.
	 *
	 * @param 	KDispatcherContextInterface $context The active command context
	 * @return 	void
	 */
	protected function _beforeSend(KDispatcherContextInterface $context)
	{
        $response = $context->response;
        $request  = $context->request;

        if(!$request->isAjax() && $response->isSuccess() && $response->getStatusCode() != KHttpResponse::NO_CONTENT) {
            $response->setRedirect($request->getReferrer());
        }
	}
}