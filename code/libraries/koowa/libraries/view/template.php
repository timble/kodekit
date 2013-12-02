<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Template View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
abstract class KViewTemplate extends KViewAbstract
{
    /**
     * Template identifier (com://APP/COMPONENT.template.NAME)
     *
     * @var string|object
     */
    protected $_template;

    /**
     * Layout name
     *
     * @var string
     */
    protected $_layout;

    /**
     * Auto assign
     *
     * @var boolean
     */
    protected $_auto_fetch;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the auto fetch
        $this->_auto_fetch = $config->auto_fetch;

        //Set the layout
        $this->setLayout($config->layout);

        //Set the template object
        $this->setTemplate($config->template);

        //Add the template filters
        $filters = (array) KObjectConfig::unbox($config->template_filters);

        foreach ($filters as $key => $value)
        {
            if (is_numeric($key)) {
                $this->getTemplate()->attachFilter($value);
            } else {
                $this->getTemplate()->attachFilter($key, $value);
            }
        }

        //Fetch the view data before rendering
        $this->registerCallback('before.render' , array($this, 'fetchData'));
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'layout'           => '',
            'template'         => $this->getName(),
            'template_filters' => array('shorttag', 'function', 'script', 'decorator', 'style', 'link', 'url'),
            'auto_fetch'       => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * @param KViewContext	$context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
        $layout  = $this->getLayout();
        $format  = $this->getFormat();

        //Handle partial layout paths
        if (is_string($layout) && strpos($layout, '.') === false)
        {
            $identifier = clone $this->getIdentifier();
            $identifier->name = $layout;
            unset($identifier->path[0]);

            $layout = (string) $identifier;
        }

        //Render the template
        $this->_content = (string) $this->getTemplate()
            ->load((string) $layout.'.'.$format)
            ->compile()
            ->evaluate($this->getData())
            ->render();

        return parent::_actionRender($context);
    }

    /**
     * Fetch the view data
     *
     * This function will always fetch the model state. Model data will only be fetched if the auto_fetch property is
     * set to TRUE.
     *
     * @param KViewContext	$context A view context object
     * @return void
     */
    public function fetchData(KViewContext $context)
    {
        $model = $this->getModel();

        //Auto-assign the state to the view
        $context->data->state = $model->getState();

        //Auto-assign the data from the model
        if($this->_auto_fetch)
        {
            //Get the view name
            $name = $this->getName();

            //Assign the data of the model to the view
            if(KStringInflector::isPlural($name))
            {
                $context->data->$name = $model->getList();
                $context->data->total = $model->getTotal();
            }
            else $context->data->$name = $model->getItem();
        }
    }

    /**
     * Get the template object attached to the view
     *
     *  @throws	UnexpectedValueException	If the template doesn't implement the TemplateInterface
     * @return  KTemplateInterface
     */
    public function getTemplate()
    {
        if (!$this->_template instanceof KTemplateInterface)
        {
            //Make sure we have a template identifier
            if (!($this->_template instanceof KObjectIdentifier)) {
                $this->setTemplate($this->_template);
            }

            $options = array(
                'view' => $this
            );

            $this->_template = $this->getObject($this->_template, $options);

            if(!$this->_template instanceof KTemplateInterface)
            {
                throw new UnexpectedValueException(
                    'Template: '.get_class($this->_template).' does not implement KTemplateInterface'
                );
            }
        }

        return $this->_template;
    }

    /**
     * Method to set a template object attached to the view
     *
     * @param   mixed   $template An object that implements KObjectInterface, an object that implements
     *                            KObjectIdentifierInterface or valid identifier string
     * @throws  UnexpectedValueException    If the identifier is not a table identifier
     * @return  KViewAbstract
     */
    public function setTemplate($template)
    {
        if (!($template instanceof KTemplateInterface))
        {
            if (is_string($template) && strpos($template, '.') === false)
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('template');
                $identifier->name = $template;
            }
            else $identifier = $this->getIdentifier($template);

            $template = $identifier;
        }

        $this->_template = $template;

        return $this;
    }

    /**
     * Get the layout
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        return empty($this->_layout) ? 'default' : $this->_layout;
    }

    /**
     * Sets the layout name to use
     *
     * @param    string  $layout The template name.
     * @return   $this
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Creates a route based on a full or partial query string.
     *
     * This function adds the layout information to the route if a layout has been set
     *
     * @param string|array $route   The query string used to create the route
     * @param boolean $fqr          If TRUE create a fully qualified route. Default TRUE.
     * @param boolean $escape       If TRUE escapes the route for xml compliance. Default TRUE.
     * @return KHttpUrl             The route
     */
    public function getRoute($route = '', $fqr = true, $escape = true)
    {
        if(is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        if (count($parts) && !isset($parts['layout']) && !empty($this->_layout))
        {
            if (!isset($parts['view']) || ($parts['view'] == $this->getName()))
            {
                if (is_array($route)) {
                    $route[] = 'layout=' . $this->getLayout();
                } else {
                    $route .= '&layout=' . $this->getLayout();
                }
            }
        }

        return parent::getRoute($route, $fqr, $escape);
    }
}
