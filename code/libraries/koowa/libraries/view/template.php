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
     * Auto assign
     *
     * @var boolean
     */
    protected $_auto_assign;

    /**
     * The assigned data
     *
     * @var boolean
     */
    protected $_data;

    /**
     * Layout name
     *
     * @var string
     */
    protected $_layout;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the auto assign state
        $this->_auto_assign = $config->auto_assign;

        //Set the data
        $this->_data = KObjectConfig::unbox($config->data);

        //Set the template object
        $this->_template = $config->template;

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
            'data'			   => array(),
            'template'         => $this->getName(),
            'template_filters' => array('shorttag', 'function', 'variable', 'script', 'style', 'link', 'url'),
            'auto_assign'      => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Set a view properties
     *
     * @param   string  $property The property name.
     * @param   mixed   $value    The property value.
     */
    public function __set($property, $value)
    {
        $this->_data[$property] = $value;
    }

    /**
     * Get a view property
     *
     * @param   string  $property The property name.
     * @return  string  The property value.
     */
    public function __get($property)
    {
        $result = null;
        if(isset($this->_data[$property])) {
            $result = $this->_data[$property];
        }

        return $result;
    }

    /**
     * Return the views output
     *
     * @return string 	The output of the view
     */
    public function display()
    {
        $layout  = $this->getLayout();
        $format  = $this->getFormat();
        $data    = $this->getData();

        //Handle partial layout paths
        if (is_string($layout) && strpos($layout, '.') === false)
        {
            $identifier = clone $this->getIdentifier();
            $identifier->name = $layout;

            $layout = (string) $identifier;
        }

        //Render the template
        $this->_content = (string) $this->getTemplate()
            ->load((string) $layout.'.'.$format)
            ->compile()
            ->evaluate($this->_data)
            ->render();

        return parent::display();
    }

    /**
     * Sets the view data
     *
     * @param   array $data The view data
     * @return  KViewTemplate
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Get the view data
     *
     * @return  array   The view data
     */
    public function getData()
    {
        return $this->_data;
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
                throw new \UnexpectedValueException(
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
     * Execute and return the views output
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->display();
    }

    /**
     * Supports a simple form of Fluent Interfaces. Allows you to assign variables to the view by using the variable
     * name as the method name. If the method name is a setter method the setter will be called instead.
     *
     * For example : $view->layout('foo')->title('name')->display().
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return  KViewAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        //If one argument is passed we assume a setter method is being called
        if(count($args) == 1)
        {
            if(method_exists($this, 'set'.ucfirst($method))) {
                return $this->{'set'.ucfirst($method)}($args[0]);
            } else {
                return $this->set($method, $args[0]);
            }
        }

        return parent::__call($method, $args);
    }
}
