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
    		'behaviors'  => array('editable')
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
     * Get action
     *
     * This function translates a GET request into a read or browse action. If the view name is singular a read action
     * will be executed, if plural a browse action will be executed.
     *
     * @param KControllerContextInterface $context A command context object
     * @return    string|bool    The rendered output of the view or FALSE if something went wrong
     */
    protected function _actionRender(KControllerContextInterface $context)
    {
        //Check if we are reading or browsing
        $action = KStringInflector::isSingular($this->getView()->getName()) ? 'read' : 'browse';

        //Execute the action
        $this->execute($action, $context);

        return parent::_actionRender($context);
    }

	/**
	 * Generic browse action, fetches a list
	 *
	 * @param	KControllerContextInterface $context A command context object
	 * @return 	KDatabaseRowsetInterface	A rowset object containing the selected rows
	 */
	protected function _actionBrowse(KControllerContextInterface $context)
	{
		$data = $this->getModel()->getList();
		return $data;
	}

	/**
	 * Generic read action, fetches an item
	 *
	 * @param	KControllerContextInterface $context A command context object
	 * @return 	KDatabaseRowInterface	 A row object containing the selected row
	 */
	protected function _actionRead(KControllerContextInterface $context)
	{
	    $data = $this->getModel()->getItem();
	    $name = ucfirst($this->getView()->getName());

		if($this->getModel()->getState()->isUnique() && $data->isNew()) {
		    $context->setError(new KControllerExceptionNotFound($name.' Not Found'));
		}

		return $data;
	}

	/**
	 * Generic edit action, saves over an existing item
	 *
	 * @param	KControllerContextInterface $context A command context object
	 * @return 	KDatabaseRowsetInterface A rowset object containing the updated rows
	 */
	protected function _actionEdit(KControllerContextInterface $context)
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
		else $context->setError(new KControllerExceptionNotFound('Resource Not Found'));

		return $data;
	}

	/**
	 * Generic add action, saves a new item
	 *
	 * @param	KControllerContextInterface $context A command context object
	 * @return 	KDatabaseRowInterface 	 A row object containing the new data
	 */
	protected function _actionAdd(KControllerContextInterface $context)
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
		else $context->setError(new KControllerExceptionBadRequest('Resource Already Exists'));

		return $data;
	}

	/**
	 * Generic delete function
	 *
	 * @param	KControllerContextInterface $context  A command context object
	 * @return 	KDatabaseRowsetInterface  A rowset object containing the deleted rows
	 */
	protected function _actionDelete(KControllerContextInterface $context)
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
		else  $context->setError(new KControllerExceptionNotFound('Resource Not Found'));

		return $data;
	}
}
