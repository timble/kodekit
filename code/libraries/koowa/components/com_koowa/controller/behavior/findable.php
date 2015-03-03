<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Smart Search Controller Behavior
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa\Controller\Behavior
 */
class ComKoowaControllerBehaviorFindable extends KControllerBehaviorAbstract
{
    /**
     * Joomla event dispatcher
     *
     * @var JDispatcher
     */
    protected static $_dispatcher;

    /**
     * Package name that is used in events
     *
     * @var string
     */
    protected $_package;

    /**
     * Entity name to index
     *
     * @var string
     */
    protected $_entity;

    /**
     * Category entity name
     *
     * @var string
     */
    protected $_category_entity;

    /**
     * Entity model
     *
     * @var KModelInterface
     */
    protected $_model;

    /**
     * Event context that is passed to events
     *
     * @var string
     */
    protected $_event_context;

    /**
     * Constructor.
     *
     * @param  KObjectConfig $config Configuration options
     * @throws UnexpectedValueException
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Need to do this here as mixer is not yet ready in _initialize method
        if (empty($config->package)) {
            $config->package = $this->getMixer()->getIdentifier()->package;
        }

        $this->_package  = $config->package;
        $this->_entity = $config->entity;
        $this->_model    = $config->model;
        $this->_event_context     = 'com_'.$config->package.'.'.$this->_entity;
        $this->_category_entity = $config->category_entity;

        if (empty($this->_entity)) {
            throw new UnexpectedValueException('Entity cannot be empty');
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
            'package'  => null,
            'entity' => null,
            'category_entity' => 'category'
        ))->append(array(
            'model' => KStringInflector::pluralize($config->entity)
        ));

        parent::_initialize($config);
    }

    /**
     * Returns the model
     *
     * @return KModelAbstract
     */
    protected function _getModel()
    {
        if (!$this->_model instanceof KModelInterface)
        {
            if (strpos($this->_model, '.') === false) {
                $this->_model = 'com://admin/'.$this->_package.'.model.'.$this->_model;
            }

            $this->_model = $this->getObject($this->_model);
        }

        return $this->_model;
    }

    /**
     * Gets the Joomla event dispatcher
     *
     * @return JDispatcher
     */
    protected function _getDispatcher()
    {
        if (!self::$_dispatcher)
        {
            JPluginHelper::importPlugin('finder');
            self::$_dispatcher = JDispatcher::getInstance();
        }

        return self::$_dispatcher;
    }

    /**
     * Caches the current state and access for categories before they are changed.
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeEdit(KControllerContextInterface $context)
    {
        if ($this->getMixer()->getIdentifier()->name === $this->_category_entity)
        {
            $collection = $this->getModel()->fetch();

            foreach ($collection as $entity)
            {
                $entity->old_enabled = $entity->enabled;
                $entity->old_access  = $entity->access;
            }
        }
    }

    /**
     * Modifies the index after save
     *
     * Also updates the state and access of items that belong to an edited category
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterEdit(KControllerContextInterface $context)
    {
        $name   = $this->getMixer()->getIdentifier()->name;

        foreach ($context->result as $entity)
        {
            if ($entity->getStatus() !== KDatabase::STATUS_FAILED)
            {
                if ($name === $this->_category_entity)
                {
                    if (($entity->old_enabled !== $entity->enabled) || ($entity->old_access !== $entity->access))
                    {
                        $category_context = 'com_'.$this->_package.'.'.$this->_category_entity;

                        $this->_getDispatcher()->trigger('onFinderAfterSave', array($category_context, $entity, false));
                    }
                } else {
                    $this->_getDispatcher()->trigger('onFinderAfterSave', array($this->_event_context, $entity, false));
                }
            }
        }
    }

    /**
     * Add new items to the index
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity  = $context->result;
        $name    = $entity->getIdentifier()->name;

        if ($name === $this->_entity && $entity->getStatus() !== KDatabase::STATUS_FAILED) {
            $this->_getDispatcher()->trigger('onFinderAfterSave', array($this->_event_context, $entity, true));
        }
    }

    /**
     * Delete items from the index
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterDelete(KControllerContextInterface $context)
    {
        $name = $this->getMixer()->getIdentifier()->name;

        if ($name === $this->_entity)
        {
            foreach ($context->result as $entity)
            {
                if ($entity->getStatus() === KDatabase::STATUS_DELETED) {
                    $this->_getDispatcher()->trigger('onFinderAfterDelete', array($this->_event_context, $entity));
                }
            }
        }
    }
}