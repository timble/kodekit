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
     * Callback for escaping.
     *
     * @var string
     */
    protected $_escape;

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
     * The view scripts
     *
     * @var array
     */
    protected $_scripts = array();

    /**
     * The view styles
     *
     * @var array
     */
    protected $_styles = array();

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

         //User-defined escaping callback
        $this->setEscape($config->escape);

        //Set the template object
        $this->_template = $config->template;

        //Add the template filters
        $filters = (array) KObjectConfig::unbox($config->template_filters);

        foreach ($filters as $key => $value)
        {
            if (is_numeric($key)) {
                $this->getTemplate()->addFilter($value);
            } else {
                $this->getTemplate()->addFilter($key, $value);
            }
        }

        //Set base and media urls for use by the view
        $this->assign('baseurl' , $config->base_url)
             ->assign('mediaurl', $config->media_url);

        //Add alias filter for media:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('media://' => $config->media_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );

        //Add alias filter for base:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('base://' => $config->base_url.'/'), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
        );
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
            'escape'           => 'htmlspecialchars',
            'template'         => $this->getName(),
            'template_filters' => array('shorttag', 'alias', 'variable', 'script', 'style', 'link', 'template'),
            'auto_assign'      => true,
            'base_url'         => KRequest::base(),
            'media_url'        => KRequest::root().'/media',
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
    * Assigns variables to the view script via differing strategies.
    *
    * This method is overloaded; you can assign all the properties of an object, an associative array, or a single value
     * by name.
    *
    * You are not allowed to set variables that begin with an underscore; these are either private properties for KView
     * or private variables within the template script itself.
    *
    * <code>
    * $view = new KViewDefault();
    *
    * // assign directly
    * $view->var1 = 'something';
    * $view->var2 = 'else';
    *
    * // assign by name and value
    * $view->assign('var1', 'something');
    * $view->assign('var2', 'else');
    *
    * // assign by assoc-array
    * $ary = array('var1' => 'something', 'var2' => 'else');
    * $view->assign($obj);
    *
    * // assign by object
    * $obj = new stdClass;
    * $obj->var1 = 'something';
    * $obj->var2 = 'else';
    * $view->assign($obj);
    *
    * </code>
    *
    * @return KViewAbstract
    */
    public function assign()
    {
        // get the arguments; there may be 1 or 2.
        $arg0 = @func_get_arg(0);
        $arg1 = @func_get_arg(1);

        // assign by object or array
        if (is_object($arg0) || is_array($arg0)) {
            $this->set($arg0);
        }

        // assign by string name and mixed value.
        elseif (is_string($arg0) && substr($arg0, 0, 1) != '_' && func_num_args() > 1) {
            $this->set($arg0, $arg1);
        }

        return $this;
    }

    /**
     * Escapes a value for output in a view script.
     *
     * @param  mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    public function escape($var)
    {
        return call_user_func($this->_escape, $var);
    }

    /**
     * Return the views output
     *
     * @return string 	The output of the view
     */
    public function display()
    {
        $this->_content = $this->getTemplate()
            ->loadIdentifier($this->_layout, $this->_data)
            ->render();

        return parent::display();
    }

     /**
     * Sets the _escape() callback.
     *
     * @param   mixed $spec The callback for _escape() to use.
     * @return  KViewAbstract
     */
    public function setEscape($spec)
    {
        $this->_escape = $spec;
        return $this;
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
     * Sets the layout name
     *
     * @param    string  $layout The template name.
     * @return   KViewAbstract
     */
    public function setLayout($layout)
    {
        if(is_string($layout) && strpos($layout, '.') === false )
		{
            $identifier = clone $this->getIdentifier();
            $identifier->name = $layout;
	    }
		else $identifier = $this->getIdentifier($layout);

        $this->_layout = $identifier;
        return $this;
    }

	/**
     * Get the layout.
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        return $this->_layout->name;
    }

    /**
     * Get the identifier for the template with the same name
     *
     * @return  KTemplateInterface
     */
    public function getTemplate()
    {
        if(!$this->_template instanceof KTemplateInterface)
        {
            //Make sure we have a template identifier
            if(!($this->_template instanceof KObjectIdentifier)) {
                $this->setTemplate($this->_template);
            }

            $options = array(
            	'view' => $this,
                'translator' => $this->getTranslator()
            );

            $this->_template = $this->getService($this->_template, $options);
        }

        return $this->_template;
    }

    /**
     * Method to set a template object attached to the view
     *
     * @param   mixed   $template An object that implements KObjectInterface, an object that
     *                  implements KObjectIdentifierInterface or valid identifier string
     * @throws  UnexpectedValueException    If the identifier is not a table identifier
     * @return  KViewAbstract
     */
    public function setTemplate($template)
    {
        if(!($template instanceof KTemplateAbstract))
        {
            if(is_string($template) && strpos($template, '.') === false )
		    {
			    $identifier = clone $this->getIdentifier();
                $identifier->path = array('template');
                $identifier->name = $template;
			}
			else $identifier = $this->getIdentifier($template);

            if($identifier->path[0] != 'template') {
                throw new UnexpectedValueException('Identifier: '.$identifier.' is not a template identifier');
            }

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
