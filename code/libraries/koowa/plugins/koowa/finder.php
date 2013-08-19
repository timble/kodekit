<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder Plugin
 *
 * Finder plugin adapter for Koowa extensions.
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Plugin\Finder
 */
abstract class PlgKoowaFinder extends FinderIndexerAdapter
{
    /**
     * Package name
     * @var string
     */
    protected $package;

    /**
     * Resource that the plugin will act on
     * @var string
     */
    protected $resource;

    /**
     * Model identifier/model object
     * @var KObjectIdentifier
     */
    protected $model;

    /**
     * Model identifier/model object
     * @var KObjectIdentifier
     */
    protected $category_model;

    /**
     * Array of instructions. These are used to tell the indexer to include certain properties their importance
     * @var array
     */
    protected $instructions = array();

    /**
     * Constructor
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An array that holds the plugin configuration
     */
    public function __construct(&$subject, $config)
    {
        if ($this->bootFramework())
        {
            $configuration = new KObjectConfig();

            $this->_initialize($configuration);

            foreach ($configuration as $key => $value) {
                $this->$key = KObjectConfig::unbox($value);
            }
        }

        parent::__construct($subject, $config);

        $this->loadLanguage();
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'package' => strtolower(substr(get_class($this), 9)) // Remove plgFinder from class name
        ))->append(array(
                'resource' => $config->package,
        ))->append(array(
            'layout'     => $config->resource,
            'model'      => KStringInflector::pluralize($config->resource),
            'category_model' => 'categories',
            'context'    => $config->package,
            'extension'  => 'com_'.$config->package,
            'type_title' => ucfirst($config->resource),
            'instructions' => array(
                FinderIndexer::TITLE_CONTEXT => array('meta_title'),
                FinderIndexer::TEXT_CONTEXT => array('description'),
                FinderIndexer::META_CONTEXT => array('meta_keywords', 'meta_description'),
                FinderIndexer::PATH_CONTEXT => array('slug')
            )
        ));
    }

    /**
     * Method to remove the link information for items that have been deleted.
     *
     * @param   string  $context  The context of the action being performed.
     * @param   JTable  $table    A JTable object containing the record to be deleted
     * @throws  Exception on database error.
     * @return  boolean  True on success.
     */
    public function onFinderAfterDelete($context, $table)
    {
        if ($context === $this->extension.'.'.$this->resource) {
            $id = $table->id;
        }
        elseif ($context == 'com_finder.index') {
            $id = $table->link_id;
        }
        else {
            return true;
        }

        // Remove the items.
        return $this->remove($id);
    }

    /**
     * Method to determine if the access level of an item changed.
     *
     * @param   string   $context  The context of the content passed to the plugin.
     * @param   JTable   $row      A JTable object
     * @param   boolean  $isNew    If the content has just been created
     * @throws  Exception on database error.
     * @return  boolean  True on success.
     */
    public function onFinderAfterSave($context, $row, $isNew)
    {
        // We only want to handle articles here
        if ($context == $this->extension.'.'.$this->resource)
        {
            // Reindex the item
            $this->reindex($row->id);
        }

        if ($context == $this->extension.'.category')
        {
            if (!$isNew && $row->access_changed) {
                $this->categoryAccessChange($row);
            }
        }

        return true;
    }

    /**
     * Method to update the link information for items that have been changed from outside the edit screen. This is
     * fired when the item is published, unpublished, archived, or unarchived from the list view.
     *
     * @param   string   $context  The context for the content passed to the plugin.
     * @param   array    $pks      A list of primary key ids of the content that has changed state.
     * @param   integer  $value    The value of the state that the content has been changed to.
     * @return  void
     */
    public function onFinderChangeState($context, $pks, $value)
    {
        // Handle when the plugin is disabled
        if ($context == 'com_plugins.plugin' && $value === 0)
        {
            // TODO: handle this case
            $this->pluginDisable($pks);
        }
    }

    /**
     * Method to update the item link information when the item category is changed. This is fired when the item
     * category is published or unpublished from the list view.
     *
     * @param   string   $extension  The extension whose category has been updated.
     * @param   array    $pks        A list of primary key ids of the content that has changed state.
     * @param   integer  $value      The value of the state that the content has been changed to.
     * @return  void
     */
    public function onFinderCategoryChangeState($extension, $pks, $value)
    {
        if ($extension == $this->extension) {
            $this->categoryStateChange($pks, $value);
        }
    }

    /**
     * Main index function run when indexing happens
     *
     * @param FinderIndexerResult $item
     * @return bool|void
     */
    protected function index(FinderIndexerResult $item)
    {
        // Check if the extension is enabled
        if (JComponentHelper::isEnabled($this->extension) == false) {
            return;
        }

        //Add the instructions
        foreach ($this->instructions AS $type => $instructions)
        {
            foreach ($instructions AS $instruction) {
                $item->addInstruction($type, $instruction);
            }
        }

        // Add the type taxonomy data.
        $item->addTaxonomy('Type', $this->type_title);

        FinderIndexerHelper::getContentExtras($item);

        // Index the item.
        if (method_exists('FinderIndexer', 'getInstance')) {
            FinderIndexer::getInstance()->index($item);
        } else {
            FinderIndexer::index($item);
        }
    }

    /**
     * Method to setup the indexer to be run.
     *
     * @return  boolean  True on success.
     */
    protected function setup()
    {
        if (!$this->bootFramework()) {
            return false;
        }

        return true;
    }

    /**
     * Boots the Koowa framework if the plugin is running in CLI mode
     *
     * @return bool
     */
    protected function bootFramework()
    {
        // This is useful in CLI mode
        if (!class_exists('Koowa'))
        {
            if (!defined('JDEBUG')) {
                define('JDEBUG', 0);
            }

            JPluginHelper::importPlugin('system');

            JDispatcher::getInstance()->trigger('onAfterInitialiase');
        }

        return class_exists('Koowa');
    }

    /**
     * Method to update index data on category access level changes
     *
     * @param   JTable  $category  A JTable object
     * @return  void
     */
    protected function categoryAccessChange($category)
    {
        $rowset = $this->getModel()->category($category->id)->getList();

        // Adjust the access level for each item within the category.
        foreach ($rowset as $row)
        {
            // Set the access level.
            $temp = max($row->access, $category->new_access);

            // Update the item.
            $this->change((int) $row->id, 'access', $temp);

            // Reindex the item
            $this->reindex($row->id);
        }
    }

    /**
     * Method to update index data on category access level changes
     *
     * @param   array    $pks    A list of primary key ids of the content that has changed state.
     * @param   integer  $value  The value of the state that the content has been changed to.
     * @return  void
     */
    protected function categoryStateChange($pks, $value)
    {
        // The item's published state is tied to the category
        // published state so we need to look up all published states
        // before we change anything.
        foreach ($pks as $pk)
        {
            $rowset = $this->getModel()->category($pk)->getList();

            // Adjust the state for each item within the category.
            foreach ($rowset as $row)
            {
                $temp = $value;

                // Update the item.
                $this->change($row->id, 'state', $temp);

                // Reindex the item
                $this->reindex($row->id);
            }
        }
    }

    /**
     * Method to get the number of content items available to index.
     *
     * @throws  Exception on database error.
     * @return  integer  The number of content items available to index.
     */
    protected function getContentCount()
    {
        return $this->getModel()->getTotal();
    }

    /**
     * Method to get a content item to index.
     *
     * @param   integer  $id  The id of the content item.
     * @return  FinderIndexerResult  A FinderIndexerResult object.
     */
    protected function getItem($id)
    {
        $row = $this->getModel()->id($id)->getItem();

        return $this->getFinderItem($row);
    }

    /**
     * Method to get a list of content items to index.
     *
     * @param   integer         $offset  The list offset.
     * @param   integer         $limit   The list limit.
     * @param   JDatabaseQuery  $query   A JDatabaseQuery object. [optional]
     * @throws  Exception on database error.
     * @return  array  An array of FinderIndexerResult objects.
     */
    protected function getItems($offset, $limit, $query = null)
    {
        $rowset = $this->getModel()
            ->limit($limit)
            ->offset($offset)
            ->getList();

        $results = array();
        foreach ($rowset AS $row) {
            $results[] = $this->getFinderItem($row);

        }

        return $results;
    }

    /**
     * Returns the model
     *
     * @return KModelAbstract
     */
    protected function getModel()
    {
        if (!$this->model instanceof KModelAbstract)
        {
            if (strpos($this->model, '.') === false) {
                $this->model = 'com://admin/'.$this->package.'.model.'.$this->model;
            }

            $this->model = KObjectManager::getObject($this->model);
        }

        return $this->model;
    }

    /**
     * Returns the category model
     *
     * @return KModelAbstract
     */
    protected function getCategoryModel()
    {
        if (!$this->category_model instanceof KModelAbstract)
        {
            if (strpos($this->category_model, '.') === false) {
                $this->category_model = 'com://admin/'.$this->package.'.model.'.$this->category_model;
            }

            $this->category_model = KObjectManager::getObject($this->category_model);
        }

        return $this->category_model;
    }

    /**
     * Turns a standard KDatabaseRow into a finder item
     *
     * @param KDatabaseRowInterface $row
     * @return object
     */
    protected function getFinderItem(KDatabaseRowInterface $row)
    {
        $data = $row->getData();

        //Get the indexer result item
        $item = JArrayHelper::toObject($data, 'FinderIndexerResult');

        $item->url = $this->getURL($item->id, $this->extension, $this->layout);
        $item->route = $this->getLink($row);
        $item->path = FinderIndexerHelper::getContentPath($item->route);

        if ($item->description) {
            // Trigger the onContentPrepare event.
            $item->summary = FinderIndexerHelper::prepareContent($item->description, $item->params);
        }

        // Translate the state. Articles should only be published if the category is published.
        $item->state = min($item->enabled, $item->category_enabled);

        // Set the item type.
        $item->type_id = $this->type_id;

        // Set the mime type.
        $item->mime = $this->mime;

        // Set the item layout.
        $item->layout = $this->layout;

        // Set the extension if present
        if (isset($row->extension)) {
            $item->extension = $row->extension;
        }

        return $item;
    }

    /**
     * Returns a link to a row
     * @param KDatabaseRowInterface $row
     * @return string
     */
    protected function getLink(KDatabaseRowInterface $row)
    {
        return sprintf('index.php?option=%s&view=%s&slug=%s', $this->extension, $this->resource, $row->slug);
    }
}
