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
  * Abstract Template
  *
  * @author  Johan Janssens <https://github.com/johanjanssens>
  * @package Kodekit\Library\Template
  */
abstract class TemplateAbstract extends ObjectAbstract implements TemplateInterface
{
    /**
     * List of template functions
     *
     * @var array
     */
    private $__functions;

    /**
     * The template data
     *
     * @var array
     */
    private $__data;

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

        //Reset the data
        $this->__data = array();

        //Register the functions
        $functions = ObjectConfig::unbox($config->functions);

        foreach ($functions as $name => $callback) {
            $this->registerFunction($name, $callback);
        }
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
            'functions' => array()
        ));

        parent::_initialize($config);
    }

    /**
     * Render a template
     *
     * @param   string  $source The template url or content
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @return  string  The rendered template source
     */
    public function render($source, array $data = array())
    {
        $this->__data = $data;

        return $source;
    }

    /**
     * Get a template property
     *
     * @param   string  $property The property name.
     * @param   mixed   $default  Default value to return.
     * @return  string  The property value.
     */
    public function get($property, $default = null)
    {
        return isset($this->__data[$property]) ? $this->__data[$property] : $default;
    }

    /**
     * Get the template data
     *
     * @return  array   The template data
     */
    public function getData()
    {
        return $this->__data;
    }

    /**
     * Register a function
     *
     * @param string $name The function name
     * @param string $function The callable
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function registerFunction($name, $function)
    {
        if (!is_callable($function))
        {
            throw new \InvalidArgumentException(
                'The function must be a callable, "'.gettype($function).'" given.'
            );
        }

        $this->__functions[$name] = $function;
        return $this;
    }

    /**
     * Unregister a function
     *
     * @param string    $name   The function name
     * @return TemplateAbstract
     */
    public function unregisterFunction($name)
    {
        if(isset($this->__functions[$name])) {
            unset($this->__functions[$name]);
        }

        return $this;
    }

    /**
     * Get the registered functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->__functions;
    }

    /**
     * Get a template data property
     *
     * @param   string  $property The property name.
     * @return  string  The property value.
     */
    final public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Call template functions
     *
     * This method will not throw a \BadMethodCallException. Instead if the method is not callable it will return null
     *
     * @param  string $method    The function name
     * @param  array  $arguments The function arguments
     * @return mixed|null   Return NULL If method could not be found
     */
    public function __call($method, $arguments)
    {
        $functions = $this->getFunctions();

        if(!isset($functions[$method]))
        {
            if (is_callable(array($this, $method))) {
                $result = parent::__call($method, $arguments);
            } else {
                $result = null;
            }
        }
        else $result = call_user_func_array($functions[$method], $arguments);

        return $result;
    }
}
