<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Persistable Dispatcher Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class KDispatcherBehaviorPersistable extends KControllerBehaviorAbstract
{
    /**
     * Get an object handle
     *
     * Disable dispatcher persistence on non-HTTP requests, e.g. AJAX. This avoids changing the model state session
     * variable of the requested model, which is often undesirable under these circumstances.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        $result = null;

        if($this->getRequest()->isGet() && !$this->getRequest()->isAjax()) {
            $result = parent::getHandle();
        }

        return $result;
    }

    /**
     * Load the model state from the request
     *
     * This functions merges the request information with any model state information that was saved in the session and
     * returns the result.
     *
     * @param 	KDispatcherContextInterface $context The active dispatcher context
     * @return 	void
     */
    protected function _beforeGet(KDispatcherContextInterface $context)
    {
        if($this->getController() instanceof KControllerModellable)
        {
            $model      = $this->getController()->getModel();
            $identifier = $model->getIdentifier();
            $state      = (array) $context->user->get($identifier);

            //Append the data to the request object
            $query = $this->getRequest()->query;

            foreach ($state as $key => $value)
            {
                if (!isset($query->$key)) {
                    $query->$key = $value;
                }
            }

            //Push the request in the model
            $model->getState()->setValues($query->toArray());
        }
    }

    /**
     * Saves the model state in the session
     *
     * @param 	KDispatcherContextInterface $context The active dispatcher context
     * @return 	void
     */
    protected function _afterGet(KDispatcherContextInterface $context)
    {
        if($this->getController()instanceof KControllerModellable)
        {
            $model  = $this->getController()->getModel();
            $state  = $model->getState();

            $vars = array();
            foreach($state->toArray() as $var)
            {
                if(!$var->unique && !$var->internal) {
                    $vars[$var->name] = $var->value;
                }
            }

            // Built the session identifier based on the action
            $identifier = $model->getIdentifier();

            //Set the state in the user session
            $context->user->set($identifier, $vars);
        }
    }
}