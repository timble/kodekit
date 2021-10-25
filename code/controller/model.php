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
 * Abstract Model Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller
 */
abstract class ControllerModel extends ControllerView implements ControllerModellable
{
    /**
     * Model object or identifier (com://APP/COMPONENT.model.NAME)
     *
     * @var	string|object
     */
    protected $_model;

    /**
     * Constructor
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        // Set the model identifier
        $this->setModel($config->model);

        //Fetch the entity before add, edit or delete
        $this->addCommandCallback('before.add'   , '_fetchEntity');
        $this->addCommandCallback('before.edit'  , '_fetchEntity');
        $this->addCommandCallback('before.delete', '_fetchEntity');
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'toolbars' => array($this->getIdentifier()->name),
            'model'    => $this->getIdentifier()->name,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the view object attached to the controller
     *
     * If the view is not an object, an object identifier or a fully qualified identifier string and the request does
     * not contain view information try to get the view from based on the model state instead. If the model is unique
     * use a singular view name, if not unique use a plural view name.
     *
     * @return  ViewInterface
     */
    public function getView()
    {
        if(!$this->_view instanceof ViewInterface)
        {
            if(!$this->_view instanceof ObjectIdentifier)
            {
                if(is_string($this->_view) && strpos($this->_view, '.') === false )
                {
                    if(!$this->getRequest()->query->has('view'))
                    {
                        $view = $this->getIdentifier()->name;

                        if($this->getModel()->getState()->isUnique()) {
                            $view = StringInflector::singularize($view);
                        } else {
                            $view = StringInflector::pluralize($view);
                        }
                    }
                    else $view = $this->getRequest()->query->get('view', 'cmd');
                }
                else $view = $this->_view;

                //Set the view
                $this->setView($view);
            }

            //Get the view
            $view = parent::getView();

            //Set the model in the view
            $view->setModel($this->getModel());
        }

        return parent::getView();
    }

    /**
     * Get the model object attached to the controller
     *
     * @throws  \UnexpectedValueException   If the model doesn't implement the ModelInterface
     * @return  ModelInterface
     */
    public function getModel()
    {
        if(!$this->_model instanceof ModelInterface)
        {
            $this->_model = $this->getObject($this->_model);

            if(!$this->_model instanceof ModelInterface)
            {
                throw new \UnexpectedValueException(
                    'Model: '.get_class($this->_model).' does not implement ModelInterface'
                );
            }

            if ($query = $this->getRequest()->getQuery()->toArray())
            {
                // Filter the current query against internal states
                foreach ($this->_model->getState() as $state) {
                    if ($state->internal && isset($query[$state->name])) unset($query[$state->name]);
                }

                //Inject the request into the model state
                $this->_model->setState($query);
            }
        }

        return $this->_model;
    }

    /**
     * Method to set a model object attached to the controller
     *
     * @param   mixed   $model An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @return	ControllerView
     */
    public function setModel($model)
    {
        if(!($model instanceof ModelInterface))
        {
            if(is_string($model) && strpos($model, '.') === false )
            {
                // Model names are always plural
                if(StringInflector::isSingular($model)) {
                    $model = StringInflector::pluralize($model);
                }

                $identifier         = $this->getIdentifier()->toArray();
                $identifier['path'] = array('model');
                $identifier['name'] = $model;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($model);

            $model = $identifier;
        }

        $this->_model = $model;

        return $this->_model;
    }

    /**
     * Get the controller context
     *
     * @param   ControllerContextInterface $context Context to cast to a local context
     * @return  ControllerContextModel
     */
    public function getContext(ControllerContextInterface $context = null)
    {
        $context = new ControllerContextModel(parent::getContext($context));
        return $context;
    }

    /**
     * Get action
     *
     * This function translates the the request into a read or browse action. If the view name is singular a read action
     * will be executed, if plural a browse action will be executed.
     *
     * If the controller returns a string or a object that can be casted to a string the result will be returned, if not
     * the view will be asked to render the result.
     *
     * @param   ControllerContext $context  A controller context object
     * @return  string|bool The rendered output of the view or FALSE if something went wrong
     */
    protected function _actionRender(ControllerContext $context)
    {
        $result = false;

        //Check if we are reading or browsing
        $action = $this->getView()->isCollection() ? 'browse' : 'read';

        //Execute the action
        if($result = $this->execute($action, $context) !== false)
        {
            if(!is_string($result) && !(is_object($result) && method_exists($result, '__toString'))) {
                $result = parent::_actionRender($context);
            }
        }

        return $result;
    }

    /**
     * Generic browse action, fetches an entity collection
     *
     * @param   ControllerContextModel  $context A controller context object
     * @return  ModelEntityInterface An entity object containing the selected entities
     */
    protected function _actionBrowse(ControllerContext $context)
    {
        $entity = $this->getModel()->fetch();

        //Set the entity in the context
        $context->setEntity($entity);

        return $entity;
    }

    /**
     * Generic read action, fetches a single entity
     *
     * @param    ControllerContext $context A controller context object
     * @throws   ControllerExceptionResourceNotFound
     * @return   ModelEntityInterface
     */
    protected function _actionRead(ControllerContext $context)
    {
        if(!$context->result instanceof ModelEntityInterface)
        {
            if($this->getModel()->getState()->isUnique())
            {
                $entity = $this->getModel()->fetch();

                if(!count($entity))
                {
                    $name   = ucfirst($this->getView()->getName());
                    throw new ControllerExceptionResourceNotFound($name.' Not Found');
                }
            }
            else $entity = $this->getModel()->create();
        }
        else $entity = $context->result;

        //Set the entity in the context
        $context->setEntity($entity);

        return $entity;
    }

    /**
     * Generic edit action, saves over an existing entity collection
     *
     * @param   ControllerContext $context A controller context object
     * @throws  ControllerExceptionResourceNotFound   If the resource could not be found
     * @return  ModelEntityInterface
     */
    protected function _actionEdit(ControllerContext $context)
    {
        $entities = $context->entity;

        if(count($entities))
        {
            foreach($entities as $entity) {
                $entity->setProperties($context->request->data->toArray());
            }

            //Only set the reset content status if the action explicitly succeeded
            if($entities->save() === true) {
                $context->response->setStatus(HttpResponse::RESET_CONTENT);
            }
        }
        else throw new ControllerExceptionResourceNotFound('Resource could not be found');

        return $entities;
    }

    /**
     * Generic add action, saves a new entity
     *
     * @param   ControllerContextModel   $context A controller context object
     * @throws  ControllerExceptionActionFailed If the delete action failed on the data entity
     * @return  ModelEntityInterface
     */
    protected function _actionAdd(ControllerContext $context)
    {
        $entity = $context->entity;

        //Only throw an error if the action explicitly failed.
        if($entity->save() === false)
        {
            $error = $entity->getStatusMessage();
            throw new ControllerExceptionActionFailed($error ? $error : 'Add Action Failed');
        }
        else
        {
            if ($entity instanceof ModelEntityInterface)
            {
                $url = clone $context->request->getUrl();

                if ($this->getModel()->getState()->isUnique())
                {
                    $states = $this->getModel()->getState()->getValues(true);

                    foreach ($states as $key => $value) {
                        $url->query[$key] = $entity->getProperty($key);
                    }
                }
                else $url->query[$entity->getIdentityKey()] = $entity->getProperty($entity->getIdentityKey());

                $context->response->headers->set('Location', (string) $url);
                $context->response->setStatus(HttpResponse::CREATED);
            }
        }

        return $entity;
    }

    /**
     * Generic delete function, deletes an existing entity collection
     *
     * @param    ControllerContext $context A controller context object
     * @throws   ControllerExceptionResourceNotFound
     * @throws   ControllerExceptionActionFailed
     * @return   ModelEntityInterface An entity object containing the deleted entities
     */
    protected function _actionDelete(ControllerContext $context)
    {
        $entities = $context->entity;

        if(count($entities))
        {
            foreach($entities as $entity) {
                $entity->setProperties($context->request->data->toArray());
            }

            //Only throw an error if the action explicitly failed.
            if($entities->delete() === false)
            {
                $error = $entities->getStatusMessage();
                throw new ControllerExceptionActionFailed($error ? $error : 'Delete Action Failed');
            }
            else $context->response->setStatus(HttpResponse::NO_CONTENT);
        }
        else throw new ControllerExceptionResourceNotFound('Resource Not Found');

        return $entities;
    }

    /**
     * Generic count function, counts the total amount of entities
     *
     * @param    ControllerContext $context A controller context object
     * @return   integer
     */
    protected function _actionCount(ControllerContext $context)
    {
        return $this->getModel()->count();
    }

    /**
     * Fetch the model entity
     *
     * @param ControllerContextModel  $context A controller context object
     * @return void
     */
    protected function _fetchEntity(ControllerContext $context)
    {
        if(!$context->result instanceof ModelEntityInterface)
        {
            switch($context->action)
            {
                case 'add'   :
                    $context->setEntity($this->getModel()->create($context->request->data->toArray()));
                    break;

                case 'edit'  :
                case 'delete':
                    $context->setEntity($this->getModel()->fetch());
                    break;

            }

        }
        else $context->setEntity($context->result);
    }

    /**
     * Supports a simple form Fluent Interfaces. Allows you to set the request properties by using the request property
     * name as the method name.
     *
     * For example : $controller->limit(10)->browse();
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return  ControllerModel
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        //Handle action alias method
        if(in_array($method, $this->getActions()))
        {
            //Get the data
            $data = !empty($args) ? $args[0] : array();

            //Set the data in the request
            if(!($data instanceof CommandInterface))
            {
                //Make sure the data is cleared on HMVC calls
                $this->getRequest()->getData()->clear();

                //Automatic set the data in the request if an associative array is passed
                if(is_array($data) && !is_numeric(key($data))) {
                    $this->getRequest()->getData()->add($data);
                }
            }
        }

        //Check first if we are calling a mixed in method to prevent the model being
        //loaded during object instantiation.
        if(!$this->isMixedMethod($method))
        {
            //Check for model state properties
            if(isset($this->getModel()->getState()->$method))
            {
                $this->getRequest()->getQuery()->set($method, $args[0]);
                $this->getModel()->getState()->set($method, $args[0]);

                return $this;
            }
        }

        return parent::__call($method, $args);
    }
}
