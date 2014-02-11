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
     * Get an object handle
     *
     * Only attach this behavior for none AJAX POST requests.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        $result = null;
        $request = $this->getRequest();

        if($request->isPost() && !$request->isAjax() && $request->getFormat() == 'html') {
            $result = parent::getHandle();
        }

        return $result;
    }

    /**
	 * Force a GET after POST using the referrer
     *
     * Redirect if the controller has a returned a 2xx status code.
	 *
	 * @param 	KDispatcherContextInterface $context The active command context
	 * @return 	void
	 */
	protected function _beforeSend(KDispatcherContextInterface $context)
	{
        $response = $context->response;
        $request  = $context->request;

        if($response->isSuccess()) {
            $response->setRedirect($request->getReferrer());
        }
	}
}