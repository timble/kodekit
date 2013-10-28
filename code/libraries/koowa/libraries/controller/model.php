<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Model Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerModel extends KControllerView implements KControllerModellable
{
 	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
    	$config->append(array(
    		'behaviors'  => array('discoverable', 'editable')
        ));

        parent::_initialize($config);
    }

	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param	mixed	$view An object that implements KObjectInterface, KObjectIdentifier object
	 * 					or valid identifier string
	 * @return	KControllerAbstract
	 */
    public function setView($view)
	{
	    if(is_string($view) && strpos($view, '.') === false )
		{
		    if(!isset($this->getRequest()->query->view))
		    {
		        if($this->getModel()->getState()->isUnique()) {
			        $view = KStringInflector::singularize($view);
		        } else {
			        $view = KStringInflector::pluralize($view);
	    	    }
		    }
		}

		return parent::setView($view);
	}

	/**
	 * Generic browse action, fetches a list
	 *
	 * @param	KCommandContext	   $context A command context object
	 * @return 	KDatabaseRowsetInterface	A rowset object containing the selected rows
	 */
	protected function _actionBrowse(KCommandContext $context)
	{
		$data = $this->getModel()->getList();
		return $data;
	}

	/**
	 * Generic read action, fetches an item
	 *
	 * @param	KCommandContext	$context A command context object
	 * @return 	KDatabaseRowInterface	 A row object containing the selected row
	 */
	protected function _actionRead(KCommandContext $context)
	{
	    $data = $this->getModel()->getItem();
	    $name = ucfirst($this->getView()->getName());

		if($this->getModel()->getState()->isUnique() && $data->isNew()) {
		    $context->setError(new KControllerExceptionNotFound($name.' Not Found', KHttpResponse::NOT_FOUND));
		}

		return $data;
	}

	/**
	 * Generic edit action, saves over an existing item
	 *
	 * @param	KCommandContext	$context A command context object
	 * @return 	KDatabaseRowsetInterface A rowset object containing the updated rows
	 */
	protected function _actionEdit(KCommandContext $context)
	{
	    $data = $this->getModel()->getData();

	    if(count($data))
	    {
	        $data->setData(KObjectConfig::unbox($context->data));

	        //Only set the reset content status if the action explicitly succeeded
	        if($data->save() === true) {
		        $context->status = KHttpResponse::RESET_CONTENT;
		    } else {
		        $context->status = KHttpResponse::NO_CONTENT;
		    }
		}
		else $context->setError(new KControllerExceptionNotFound('Resource Not Found', KHttpResponse::NOT_FOUND));

		return $data;
	}

	/**
	 * Generic add action, saves a new item
	 *
	 * @param	KCommandContext	$context A command context object
	 * @return 	KDatabaseRowInterface 	 A row object containing the new data
	 */
	protected function _actionAdd(KCommandContext $context)
	{
		$data = $this->getModel()->getItem();

		if($data->isNew())
		{
		    $data->setData(KObjectConfig::unbox($context->data));

		    //Only throw an error if the action explicitly failed.
		    if($data->save() === false)
		    {
			    $error = $data->getStatusMessage();
		        $context->setError(new KControllerExceptionActionFailed(
		           $error ? $error : 'Add Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
		        ));

		    }
		    else $context->status = KHttpResponse::CREATED;
		}
		else $context->setError(new KControllerExceptionBadRequest('Resource Already Exists', KHttpResponse::BAD_REQUEST));

		return $data;
	}

	/**
	 * Generic delete function
	 *
	 * @param	KCommandContext	$context  A command context object
	 * @return 	KDatabaseRowsetInterface  A rowset object containing the deleted rows
	 */
	protected function _actionDelete(KCommandContext $context)
	{
	    $data = $this->getModel()->getData();

		if(count($data))
	    {
            $data->setData(KObjectConfig::unbox($context->data));

            //Only throw an error if the action explicitly failed.
	        if($data->delete() === false)
	        {
			    $error = $data->getStatusMessage();
                $context->setError(new KControllerExceptionActionFailed(
		            $error ? $error : 'Delete Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
		        ));
		    }
		    else $context->status = KHttpResponse::NO_CONTENT;
		}
		else  $context->setError(new KControllerExceptionNotFound('Resource Not Found', KHttpResponse::NOT_FOUND));

		return $data;
	}

	/**
	 * Get action
	 *
	 * This function translates a GET request into a read or browse action. If the view name is singular a read action
     * will be executed, if plural a browse action will be executed.
	 *
	 * If the result of the read or browse action is not a row or rowset object the function will pass through the
     * result, request the attached view to render itself.
	 *
	 * @param	KCommandContext	$context A command context object
	 * @return 	string|bool 	The rendered output of the view or FALSE if something went wrong
	 */
	protected function _actionGet(KCommandContext $context)
	{
		//Check if we are reading or browsing
	    $action = KStringInflector::isSingular($this->getView()->getName()) ? 'read' : 'browse';

	    //Execute the action
		$result = $this->execute($action, $context);

		//Only process the result if a valid row or rowset object has been returned
		if(($result instanceof KDatabaseRowInterface) || ($result instanceof KDatabaseRowsetInterface)) {
            $result = parent::_actionGet($context);
		}

		return (string) $result;
	}

	/**
	 * Post action
	 *
	 * This function translated a POST request action into an edit or add action. If the model state is unique a edit
     * action will be executed, if not unique an add action will be executed.
	 *
	 * @param	KCommandContext	 $context	A command context object
	 * @return 	KDatabaseRowsetInterface	A row(set) object containing the modified data
	 */
	protected function _actionPost(KCommandContext $context)
	{
		$action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
		return parent::execute($action, $context);
	}

	/**
	 * Put action
	 *
	 * This function translates a PUT request into an edit or add action. Only if the model state is unique and the
     * item exists an edit action will be executed, if the resources doesn't exist and the state is unique an add
     * action will be executed.
	 *
	 * If the resource already exists it will be completely replaced based on the data available in the request.
	 *
     * @param	KCommandContext	 $context A command context object
     * @throws  KControllerExceptionNotFound If the model state is not unique
     * @return 	KDatabaseRowsetInterface     A row(set) object containing the modified data
	 */
	protected function _actionPut(KCommandContext $context)
	{
	    $data = $this->getModel()->getItem();

	    if($this->getModel()->getState()->isUnique())
	    {
            $action = 'add';
	        if(!$data->isNew())
	        {
	            //Reset the row data
	            $data->reset();
	            $action = 'edit';
            }

            //Set the row data based on the unique state information
	        $state = $this->getModel()->getState()->getValues(true);
	        $data->setData($state);

            $data = parent::execute($action, $context);
	    }
	    else $context->setError(new KControllerExceptionNotFound(ucfirst('Resource not found', KHttpResponse::BAD_REQUEST)));

	    return $data;
	}
}
