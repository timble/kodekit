<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Smart Search Controller Behavior
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
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
     * Resource name for the item to index
     *
     * @var string
     */
    protected $_resource;

    /**
     * Event context that is passed to events
     *
     * @var string
     */
    protected $_event_context;

    /**
     * Category resource name
     *
     * @var string
     */
    protected $_category_resource;

    /**
     * Resource model
     *
     * @var KModelInterface
     */
    protected $_resource_model;

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

        $this->_package   = $config->package;
        $this->_resource  = $config->resource;
        $this->_resource_model    = $config->resource_model;
        $this->_event_context     = 'com_'.$config->package.'.'.$this->_resource;
        $this->_category_resource = $config->category_resource;

        if (empty($this->_resource)) {
            throw new UnexpectedValueException('Resource cannot be empty in finder behavior');
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
            'resource' => null,
            'category_resource' => 'category'
        ))->append(array(
            'resource_model' => KStringInflector::pluralize($config->resource)
        ));

        parent::_initialize($config);
    }

    /**
     * Returns a rowset containing items in the category
     *
     * @param  KModelEntityInterface $category Category
     * @return KModelEntityInterface
     */
    protected function _getCategoryChildren(KModelEntityInterface $category)
    {
        return $this->_getResourceModel()->category($category->id)->fetch();
    }

    /**
     * Returns the model
     *
     * @return KModelAbstract
     */
    protected function _getResourceModel()
    {
        if (!$this->_resource_model instanceof KModelInterface)
        {
            if (strpos($this->_resource_model, '.') === false) {
                $this->_resource_model = 'com://admin/'.$this->_package.'.model.'.$this->_resource_model;
            }

            $this->_resource_model = $this->getObject($this->_resource_model);
        }

        return $this->_resource_model;
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
     * Method to update index data on category access level and state changes
     *
     * @param   KModelEntityInterface  $category
     * @return  void
     */
    protected function _reindexCategory(KModelEntityInterface $category)
    {
        $dispatcher = $this->_getDispatcher();
        $collection     = $this->_getCategoryChildren($category);

        foreach ($collection as $entity) {
            $dispatcher->trigger('onFinderAfterSave', array($this->_event_context, $entity, false));
        }
    }

    /**
     * Caches the current state and access for categories before they are changed.
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeEdit(KControllerContextInterface $context)
    {
        if ($this->getMixer()->getIdentifier()->name === $this->_category_resource)
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
                if ($name === $this->_category_resource)
                {
                    if (($entity->old_enabled !== $entity->enabled) || ($entity->old_access !== $entity->access)) {
                        $this->_reindexCategory($entity);
                    }
                }

                $this->_getDispatcher()->trigger('onFinderAfterSave', array($this->_event_context, $entity, false));
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
        $name = $this->getMixer()->getIdentifier()->name;
        $entity  = $context->result;

        if ($name === $this->_resource && $entity->getStatus() !== KDatabase::STATUS_FAILED) {
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

        if ($name === $this->_resource)
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