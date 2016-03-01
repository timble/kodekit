<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Cacheable Controller Behavior
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Controller\Behavior
 */
class ComKoowaControllerBehaviorCacheable extends KControllerBehaviorAbstract
{
    /**
     * List of modules to cache
     *
     * @var	array
     */
    protected $_modules;

    /**
     * The cached state of the resource
     *
     * @var boolean
     */
    protected $_output = '';

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Set the view identifier
        $this->_modules = KObjectConfig ::unbox($config->modules);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig  $config)
    {
        $config->append(array(
            'modules'   => array('toolbar', 'title', 'submenu')
        ));

        parent::_initialize($config);
    }

    /**
     * Fetch the unrendered view data from the cache
     *
     * @param   KControllerContextInterface $context A command context object
     * @return 	void
     */
    protected function _beforeRender(KControllerContextInterface $context)
    {
        $view   = $this->getView();
        $cache  = $this->_getCache($this->_getGroup(), 'output');
        $key    = $this->_getKey();

        if($data = $cache->get($key))
        {
            $data = unserialize($data);

            //Render the view output
            if($view instanceof KViewTemplate)
            {
                $context->result = $view->getTemplate()
                    ->loadString($data['component'])
                    ->render();
            }
            else $context->result = $data['component'];

            //Render the modules
            if(isset($data['modules']))
            {
                foreach($data['modules'] as $name => $content) {
                    JFactory::getDocument()->setBuffer($content, 'modules', $name);
                }
            }

            $this->_output = $context->result;
        }
    }

    /**
     * Store the unrendered view data in the cache
     *
     * @param   KControllerContextInterface $context A command context object
     * @return 	void
     */
    protected function _afterRender(KControllerContextInterface $context)
    {
        if(empty($this->_output))
        {
            $view   = $this->getView();
            $cache  = $this->_getCache($this->_getGroup(), 'output');
            $key    = $this->_getKey();

            $data  = array();

            //Store the unrendered view output
            if($view instanceof KViewTemplate)
            {
                $data['component'] = (string) $view->getTemplate();

                $buffer = JFactory::getDocument()->getBuffer();
                if(isset($buffer['modules'])) {
                    $data['modules'] = array_intersect_key($buffer['modules'], array_flip($this->_modules));
                }
            }
            else $data['component'] = $context->result;

            $cache->store(serialize($data), $key);
        }
    }

    /**
     * Return the cached data after read
     *
     * Only if cached data was found return it but allow the chain to continue to allow
     * processing all the read commands
     *
     * @param   KControllerContextInterface $context A command context object
     * @return 	void
     */
    protected function _afterRead(KControllerContextInterface $context)
    {
        if(!empty($this->_output)) {
            $context->result = $this->_output;
        }
    }

    /**
     * Return the cached data before browse
     *
     * Only if cached data was fetch return it and break the chain to disallow any
     * further processing to take place
     *
     * @param   KControllerContextInterface $context A command context object
     * @return 	null|boolean
     */
    protected function _beforeBrowse(KControllerContextInterface $context)
    {
        if(!empty($this->_output))
        {
            $context->result = $this->_output;
            return false;
        }

        return null;
    }

    /**
     * Clean the cache
     *
     * @param   KControllerContextInterface $context A command context object
     * @return 	boolean
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $status = $context->result->getStatus();

        if($status == KDatabase::STATUS_CREATED) {
            $this->_getCache()->clean($this->_getGroup());
        }

        return true;
    }

    /**
     * Clean the cache
     *
     * @param   KControllerContextInterface $context A command context object
     * @return 	boolean
     */
    protected function _afterDelete(KControllerContextInterface $context)
    {
        $status = $context->result->getStatus();

        if($status == KDatabase::STATUS_DELETED) {
            $this->_getCache()->clean($this->_getGroup());
        }

        return true;
    }

    /**
     * Clean the cache
     *
     * @param   KControllerContextInterface $context A command context object
     * @return  boolean
     */
    protected function _afterEdit(KControllerContextInterface $context)
    {
        $status = $context->result->getStatus();

        if($status == KDatabase::STATUS_UPDATED) {
            $this->_getCache()->clean($this->_getGroup());
        }

        return true;
    }

    /**
     * Create a JCache instance
     *
     * @param string $group
     * @param string $handler
     * @param null   $storage
     * @return JCache
     */
    protected function _getCache($group = '', $handler = 'callback', $storage = null)
    {
        return JFactory::getCache($group, $handler, $storage);
    }

    /**
     * Generate a cache key
     *
     * The key is based on the layout, format and model state
     *
     * @return  string
     */
    protected function _getKey()
    {
        $view  = $this->getView();
        $state = $this->getModel()->getState()->toArray();

        $key = $view->getLayout().'-'.$view->getFormat().':'.md5(http_build_query($state, '', '&'));
        return $key;
    }

    /**
     * Generate a cache group
     *
     * The group is based on the component identifier
     *
     * @return  string
     */
    protected function _getGroup()
    {
        $group = $this->getMixer()->getIdentifier();
        return $group;
    }
}