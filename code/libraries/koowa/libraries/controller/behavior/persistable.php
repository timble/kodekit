<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Persistable Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class KControllerBehaviorPersistable extends KControllerBehaviorAbstract
{
	/**
	 * Load the model state from the request
	 *
	 * This functions merges the request information with any model state information that was saved in the session and
     * returns the result.
	 *
	 * @param 	KCommandContext $context The active command context
	 * @return 	void
	 */
	protected function _beforeBrowse(KCommandContext $context)
	{
		// Built the session identifier based on the action
		$identifier  = $this->getModel()->getIdentifier().'.'.$context->action;
		$state       = KRequest::get('session.'.$identifier, 'raw', array());

    //Append the data to the request object
    $query = $this->getRequest()->query;
    foreach ($state as $key => $value)
    {
        if (empty($query->$key)) {
            $query->$key = $value;
        }
    }

		//Push the request in the model
		$this->getModel()->setState($this->getRequest()->query->toArray());
	}

	/**
	 * Saves the model state in the session
	 *
	 * @param 	KCommandContext	$context The active command context
	 * @return 	void
	 */
	protected function _afterBrowse(KCommandContext $context)
	{
		$model  = $this->getModel();
		$state  = $model->getState()->getValues();

		// Built the session identifier based on the action
		$identifier  = $model->getIdentifier().'.'.$context->action;

		//Prevent unused state information from being persisted
		KRequest::set('session.'.$identifier, null);

		//Set the state in the session
		KRequest::set('session.'.$identifier, $state);
	}
}
