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
 * Template
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template
 */
class Template extends TemplateAbstract
{
    /**
     * The template parameters
     *
     * @var array
     */
    private $__parameters;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     *
     * @param ObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the parameters
        $this->setParameters($config->parameters);

        // Mixin the behavior (and command) interface
        $this->mixin('lib:behavior.mixin', $config);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'behaviors'  => array('helperable'),
            'parameters' => array(),
            'filters'    => array(),
            'functions'  => array(
                'escape'     => array(__NAMESPACE__.'\StringEscaper', 'escape'),
                'parameter'  => array($this, 'getParameter'),
                'parameters' => array($this, 'getParameters')
            ),
        ))->append(array(
            'behaviors'  => array('filterable' => array('filters' => $config->filters)),
        ));

        parent::_initialize($config);
    }

    /**
     * Render a template
     *
     * @param   string  $source  A template url or string content
     * @param   array   $data    An associative array of data to be extracted in local template scope
     * @param   string  $type    The template type when the source is a string and not a url
     * @return  string  The rendered template source
     */
    final public function render($source, array $data = array(), $type = null)
    {
        $context = $this->getContext();
        $context->data   = $data;
        $context->source = $source;
        $context->type   = $type;

        //If content is a path find the type by locating the file
        if($this->getObject('filter.path')->validate($source))
        {
            $locator = $this->getObject('template.locator.factory')->createLocator($source);

            if (!$file = $locator->locate($source)) {
                throw new \InvalidArgumentException(sprintf('The template "%s" cannot be located.', $source));
            }

            $context->type = pathinfo($file, PATHINFO_EXTENSION);
        }

        if ($this->invokeCommand('before.render', $context) !== false)
        {
            //Render the template
            $context->result = $this->_actionRender($context);
            $this->invokeCommand('after.render', $context);
        }

        return $context->result;
    }

    /**
     * Render the template
     *
     * @param TemplateContext   $context A template context object
     * @return string  The output of the template
     */
    protected function _actionRender(TemplateContext $context)
    {
        $source = parent::render($context->source, ObjectConfig::unbox($context->data));

        if($context->type)
        {
            $source = $this->getObject('template.engine.factory')
                ->createEngine($context->type, array('functions' => $this->getFunctions()))
                ->render($source, $this->getData());
        }

        return $source;
    }

    /**
     * Set the template parameters
     *
     * @param  array $parameters Set the template parameters
     * @return Template
     */
    public function setParameters($parameters)
    {
        $this->__parameters = new ObjectConfig($parameters);
        return $this;
    }

    /**
     * Get the template parameters
     *
     * @return ObjectConfigInterface
     */
    public function getParameters()
    {
        return $this->__parameters;
    }

    /*
     * Get a template parameter by name
     *
     * @param string $name      The name of the parameter
     * @param string $default   The default value if the parameter does not exist
     * @return ObjectConfigInterface
     */
    public function getParameter($name, $default = null)
    {
        return $this->__parameters->get($name, $default);
    }

    /**
     * Get the template context
     *
     * @param   TemplateContextInterface $context Context to cast to a local context
     * @return  TemplateContext
     */
    public function getContext(TemplateContextInterface $context = null)
    {
        $context = new TemplateContext($context);
        $context->setData($this->getData());
        $context->setParameters($this->getParameters());

        return $context;
    }
}