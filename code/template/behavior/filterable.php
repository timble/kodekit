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
 * Filterable Template Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Behavior
 */
class TemplateBehaviorFilterable extends TemplateBehaviorAbstract
{
    /**
     * List of filters
     *
     * The key holds the filter name and the value the filter object
     *
     * @var array
     */
    private $__filters = array();

    /**
     * Filter queue
     *
     * @var	ObjectQueue
     */
    private $__filter_queue;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config	An optional ObjectConfig object with configuration options.
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the filter queue
        $this->__filter_queue = $this->getObject('lib:object.queue');

        //Add the authenticators
        $filters = (array) ObjectConfig::unbox($config->filters);

        foreach ($filters as $key => $value)
        {
            if (is_numeric($key)) {
                $this->addFilter($value);
            } else {
                $this->addFilter($key, $value);
            }
        }
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'filters' => array(),
        ));
    }

    /**
     * Attach a filter for template transformation
     *
     * @param   mixed $filter An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param   array $config An optional associative array of configuration settings
     * @throws \UnexpectedValueException
     * @return TemplateAbstract
     */
    public function addFilter($filter, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($filter) && strpos($filter, '.') === false)
        {
            $identifier = $this->getIdentifier()->toArray();
            $identifier['path'] = array('template', 'filter');
            $identifier['name'] = $filter;

            $identifier = $this->getIdentifier($identifier);
        }
        else $identifier = $this->getIdentifier($filter);

        if (!$this->hasFilter($identifier->name))
        {
            $filter = $this->getObject($identifier, $config);

            if (!($filter instanceof TemplateFilterInterface))
            {
                throw new \UnexpectedValueException(
                    "Template filter $identifier does not implement TemplateFilterInterface"
                );
            }

            //Store the filter
            $this->__filters[$filter->getIdentifier()->name] = $filter;

            //Enqueue the filter
            $this->__filter_queue->enqueue($filter, $filter->getPriority());
        }

        return $this;
    }

    /**
     * Check if a filter exists
     *
     * @param 	string	$filter The name of the filter
     * @return  boolean	TRUE if the filter exists, FALSE otherwise
     */
    public function hasFilter($filter)
    {
        return isset($this->__filters[$filter]);
    }

    /**
     * Get a filter by identifier
     *
     * @param   mixed $filter       An object that implements ObjectInterface, ObjectIdentifier object
     *                              or valid identifier string
     * @throws \UnexpectedValueException
     * @return TemplateFilterInterface|null
     */
    public function getFilter($filter)
    {
        $result = null;

        if(isset($this->__filters[$filter])) {
            $result = $this->__filters[$filter];
        }

        return $result;
    }

    /**
     * Filter template content
     *
     * @param TemplateContextInterface $context	A dispatcher context object
     * @return string The filtered content
     */
    protected function _afterRender(TemplateContextInterface $context)
    {
        if(is_string($context->result))
        {
            $content = $context->result;

            foreach($this->__filter_queue as $filter) {
                $filter->filter($content, $this->getMixer());
            }

            $context->result = $content;
        }
    }
}