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
 * Abstract Template View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View
 */
abstract class ViewTemplate extends ViewAbstract  implements ViewTemplatable
{
    /**
     * Template identifier (com://APP/COMPONENT.template.NAME)
     *
     * @var string|object
     */
    private $__template;

    /**
     * Layout name
     *
     * @var string
     */
    private $__layout;

    /**
     * Constructor
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the layout
        $this->setLayout($config->layout);

        //Set the template object
        $this->setTemplate($config->template);

        //Fetch the view data before rendering
        $this->addCommandCallback('before.render', '_fetchData');
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'behaviors'          => array('localizable', 'routable'),
            'layout'             => '',
            'template'           => 'default',
            'template_filters'   => array('asset'),
            'template_functions' => array(
                'url'      => array($this, 'getUrl'),
                'title'    => array($this, 'getTitle'),
                'content'  => array($this, 'getContent'),
                'language' => array($this, 'getLanguage')
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * @param ViewContextTemplate  $context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(ViewContextTemplate $context)
    {
        $data = ObjectConfig::unbox($context->data);
        $path = $this->qualifyLayout($context->layout);

        //Render the template
        $content = $this->getTemplate()
            ->setParameters($context->parameters)
            ->render($path, $data);

        return $content;
    }

    /**
     * Fetch the view data
     *
     * @param ViewContextTemplate  $context A view context object
     * @return void
     */
    protected function _fetchData(ViewContextTemplate $context)
    {
        $model = $this->getModel();

        //Set the data
        $name = $this->getName();
        $context->data->$name = $context->entity;

        //Set the parameters
        if($this->isCollection())
        {
            $context->parameters->merge($model->getState()->getValues());
            $context->parameters->total = $model->count();
        }
        else
        {
            $context->parameters->merge($context->entity->getProperties());
            $context->parameters->total = 1;
        }

        //Set the layout and view in the parameters.
        $context->parameters->layout = $context->layout;
        $context->parameters->view   = $this->getName();
    }

    /**
     * Get the template object attached to the view
     *
     * @throws \UnexpectedValueException    If the template doesn't implement the TemplateInterface
     * @return  TemplateInterface
     */
    public function getTemplate()
    {
        if (!$this->__template instanceof TemplateInterface)
        {
            //Make sure we have a template identifier
            if (!($this->__template instanceof ObjectIdentifier)) {
                $this->setTemplate($this->__template);
            }

            $options = array(
                'filters'   => $this->getConfig()->template_filters,
                'functions' => $this->getConfig()->template_functions,
            );

            $this->__template = $this->getObject($this->__template, $options);

            if(!$this->__template instanceof TemplateInterface)
            {
                throw new \UnexpectedValueException(
                    'Template: '.get_class($this->__template).' does not implement TemplateInterface'
                );
            }
        }

        return $this->__template;
    }

    /**
     * Method to set a template object attached to the view
     *
     * @param   mixed   $template An object that implements ObjectInterface, an object that implements
     *                            ObjectIdentifierInterface or valid identifier string
     * @throws  \UnexpectedValueException    If the identifier is not a table identifier
     * @return  ViewAbstract
     */
    public function setTemplate($template)
    {
        if (!($template instanceof TemplateInterface))
        {
            if (is_string($template) && strpos($template, '.') === false)
            {
                $identifier = $this->getIdentifier()->toArray();
                $identifier['path'] = array('template');
                $identifier['name'] = $template;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($template);

            $template = $identifier;
        }

        $this->__template = $template;

        return $this;
    }

    /**
     * Get the layout
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        return empty($this->__layout) ? 'default' : $this->__layout;
    }

    /**
     * Sets the layout name to use
     *
     * @param    string  $layout The template name.
     * @return   $this
     */
    public function setLayout($layout)
    {
        $this->__layout = $layout;
        return $this;
    }

    /**
     * Qualify the layout
     *
     * Convert a relative layout URL into an absolute layout URL
     *
     * @param string $layout The view layout name
     * @param string $type   The filesystem locator type
     * @return string   The fully qualified template url
     */
    public function qualifyLayout($layout, $type = 'com')
    {
        $layout = (string) $layout;

        //Handle partial layout paths
        if(!parse_url($layout, PHP_URL_SCHEME))
        {
            $package = $this->getIdentifier()->package;
            $domain  = $this->getIdentifier()->domain;
            $format  = $this->getFormat();

            $path = $this->getIdentifier()->getPath();
            array_shift($path); //remove 'view'
            $path[] = basename($layout);

            $path = implode('/', $path);

            if($domain) {
                $layout = $type.'://'.$domain .'/' . $package . '/' .$path;
            } else {
                $layout = $type.':' . $package . '/' .$path;
            }

            $layout = $layout.'.'.$format;
        }

        return $layout;
    }

    /**
     * Set the view url
     *
     * Ensure the url is properly escaped
     *
     * @param HttpUrl $url   A HttpUrl object or a string
     * @return  ViewAbstract
     */
    public function setUrl(HttpUrl $url)
    {
        $url->setEscaped(true);
        return parent::setUrl($url);
    }

    /**
     * Get the view context
     *
     * @return  ViewContextTemplate
     */
    public function getContext()
    {
        $context = new ViewContextTemplate(parent::getContext());
        $context->setLayout($this->getLayout());

        return $context;
    }
}