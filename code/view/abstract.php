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
 * Abstract View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View
 */
abstract class ViewAbstract extends Object implements ViewInterface, CommandCallbackDelegate
{
    /**
     * Model identifier (com://APP/COMPONENT.model.NAME)
     *
     * @var	string|object
     */
    private $__model;

    /**
     * The uniform resource locator
     *
     * @var HttpUrl
     */
    private $__url;

    /**
     * The content of the view
     *
     * @var string
     */
    private $__content;

    /**
     * The title of the view
     *
     * @var string
     */
    private $__title;

    /**
     * The view data
     *
     * @var array
     */
    private $__data;

    /**
     * The view parameters
     *
     * @var array
     */
    private $__parameters;

    /**
     * The mimetype
     *
     * @var string
     */
    private $__mimetype;

    /**
     * Constructor
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the data
        $this->__data = ObjectConfig::unbox($config->data);

        //Set the parameters
        $this->__parameters = ObjectConfig::unbox($config->parameters);

        $this->setUrl($config->url);
        $this->setTitle($config->title);
        $this->setContent($config->content);
        $this->setMimetype($config->mimetype);

        $this->setModel($config->model);

        // Mixin the behavior (and command) interface
        $this->mixin('lib:behavior.mixin', $config);
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
            'data'             => array(),
            'parameters'       => array(),
            'model'      => 'lib:model.empty',
            'content'    => '',
            'mimetype'   => 'application/octet-stream ',
            'url'        =>  $this->getObject('lib:http.url'),
            'title'      => ucfirst($this->getName()),
            'behaviors'  => array('lib:behavior.eventable')
        ));

        parent::_initialize($config);
    }

    /**
     * Execute an action by triggering a method in the derived class.
     *
     * @param   array $data The view data
     * @return  string  The output of the view
     */
    final public function render($data = array())
    {
        $context = $this->getContext();
        $context->data = array_merge($this->getData(), $data);

        if ($this->invokeCommand('before.render', $context) !== false)
        {
            //Render the view
            $context->result = $this->_actionRender($context);
            $this->invokeCommand('after.render', $context);
        }

        //Set the content
        $this->setContent($context->result);

        return $context->result;
    }

    /**
     * Invoke a command handler
     *
     * @param string             $method    The name of the method to be executed
     * @param CommandInterface  $command   The command
     * @return mixed Return the result of the handler.
     */
    public function invokeCommandCallback($method, CommandInterface $command)
    {
        return $this->$method($command);
    }

    /**
     * Render the view
     *
     * @param ViewContext   $context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(ViewContext $context)
    {
        return trim($context->content);
    }

    /**
     * Set a view property
     *
     * @param   string $property The property name.
     * @param   mixed  $value    The property value.
     * @return ViewAbstract
     */
    public function set($property, $value)
    {
        $this->__data[$property] = $value;
        return $this;
    }

    /**
     * Get a view property
     *
     * @param  string $property The property name.
     * @param  mixed  $default  Default value to return.
     * @throws \InvalidArgumentException
     * @return string  The property value.
     */
    public function get($property, $default = null)
    {
        return isset($this->__data[$property]) ? $this->__data[$property] : $default;
    }

    /**
     * Check if a view property exists
     *
     * @param   string  $property   The property name.
     * @return  boolean TRUE if the property exists, FALSE otherwise
     */
    public function has($property)
    {
        return isset($this->__data[$property]);
    }

    /**
     * Get the view data
     *
     * @return  array   The view data
     */
    public function getData()
    {
        return $this->__data;
    }

    /**
     * Sets the view data
     *
     * @param   array $data The view data
     * @return  ViewAbstract
     */
    public function setData($data)
    {
        foreach($data as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Get the view parameters
     *
     * @return  array   The view parameters
     */
    public function getParameters()
    {
        return $this->__parameters;
    }

    /**
     * Sets the view parameters
     *
     * @param   array $parameters The view parameters
     * @return  ViewAbstract
     */
    public function setParameters(array $parameters)
    {
        $this->__parameters = $parameters;
        return $this;
    }

    /**
     * Get the title
     *
     * @return 	string 	The title of the view
     */
    public function getTitle()
    {
        return $this->__title;
    }

    /**
     * Set the title
     *
     * @return 	string 	The title of the view
     */
    public function setTitle($title)
    {
        $this->__title = $title;
        return $this;
    }

    /**
     * Get the content
     *
     * @return  string The content of the view
     */
    public function getContent()
    {
        return $this->__content;
    }

    /**
     * Get the contents
     *
     * @param  string $content The contents of the view
     * @return ViewAbstract
     */
    public function setContent($content)
    {
        $this->__content = $content;
        return $this;
    }

    /**
     * Get the model object attached to the view
     *
     * @throws  \UnexpectedValueException    If the model doesn't implement the ModelInterface
     * @return  ModelInterface
     */
    public function getModel()
    {
        if(!$this->__model instanceof ModelInterface)
        {
            $this->__model = $this->getObject($this->__model);

            if(!$this->__model instanceof ModelInterface)
            {
                throw new \UnexpectedValueException(
                    'Model: '.get_class($this->__model).' does not implement ModelInterface'
                );
            }
        }

        return $this->__model;
    }

    /**
     * Method to set a model object attached to the controller
     *
     * @param   mixed   $model An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @return	ViewAbstract
     */
    public function setModel($model)
    {
        if(!($model instanceof ModelInterface))
        {
            if(is_string($model) && strpos($model, '.') === false )
            {
                // Model names are always plural
                if(StringInflector::isSingular($model)) {
                    $model = StringInflector::pluralize($model);
                }

                $identifier         = $this->getIdentifier()->toArray();
                $identifier['path'] = array('model');
                $identifier['name'] = $model;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($model);

            $model = $identifier;
        }

        $this->__model = $model;

        return $this;
    }

    /**
     * Get the view url
     *
     * @return  HttpUrl  A HttpUrl object
     */
    public function getUrl()
    {
        return $this->__url;
    }

    /**
     * Set the view url
     *
     * @param HttpUrl $url   A HttpUrl object or a string
     * @return  ViewAbstract
     */
    public function setUrl(HttpUrl $url)
    {
        //Remove the user and pass from the view url
        unset($url->user);
        unset($url->pass);
        unset($url->port);

        $this->__url = $url;
        return $this;
    }

    /**
     * Get the view context
     *
     * @param   ViewContextInterface $context Context to cast to a local context
     * @return  ViewContext
     */
    public function getContext(ViewContextInterface $context = null)
    {
        $context = new ViewContext($context);
        $context->setEntity($this->getModel()->fetch());
        $context->setData($this->getData());
        $context->setParameters($this->getParameters());

        return $context;
    }

    /**
     * Get the name
     *
     * @return  string  The name of the view
     */
    public function getName()
    {
        $total = count($this->getIdentifier()->path);
        return $this->getIdentifier()->path[$total - 1];
    }

    /**
     * Get the format
     *
     * @return  string  The format of the view
     */
    public function getFormat()
    {
        return $this->getIdentifier()->name;
    }

    /**
     * Get the mimetype
     *
     * @return  string The mimetype of the view
     */
    public function getMimetype()
    {
        return $this->__mimetype;
    }
    
    /**
     * Set the mimetype
     *
     * @return ViewAbstract
     */
    public function setMimetype($mimetype)
    {
        $this->__mimetype = $mimetype;
        return $this;
    }

    /**
     * Returns the views output
     *
     * @return string
     */
    public function toString()
    {
        return $this->render();
    }

    /**
     * Check if we are rendering an entity collection
     *
     * @return bool
     */
    public function isCollection()
    {
        return StringInflector::isPlural($this->getName());
    }

    /**
     * Set a view data property
     *
     * @param   string  $property The property name.
     * @param   mixed   $value    The property value.
     */
    final public function __set($property, $value)
    {
        $this->set($property, $value);
    }

    /**
     * Get a view data property
     *
     * @param   string  $property The property name.
     * @return  string  The property value.
     */
    final public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Test existence of a view data property
     *
     * @param  string $name The property name.
     * @return boolean
     */
    final public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Returns the views output
     *
     * @return string
     */
    final public function __toString()
    {
        $result = '';

        //Not allowed to throw exceptions in __toString() See : https://bugs.php.net/bug.php?id=53648
        try {
            $result = $this->toString();
        } catch (Exception $e) {
            trigger_error('ViewAbstract::__toString exception: '. (string) $e, E_USER_ERROR);
        }

        return $result;
    }

    /**
     * Supports a simple form of Fluent Interfaces. Allows you to assign variables to the view by using the variable
     * name as the method name. If the method name is a setter method the setter will be called instead.
     *
     * For example : $view->data(array('foo' => 'bar'))->title('name')->render()
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return  ViewAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        if (!$this->isMixedMethod($method))
        {
            //If one argument is passed we assume a setter method is being called
            if (count($args) == 1)
            {
                if (!method_exists($this, 'set' . ucfirst($method)))
                {
                    $this->$method = $args[0];
                    return $this;
                }
                else return $this->{'set' . ucfirst($method)}($args[0]);
            }

            //Check if a behavior is mixed
            $parts = StringInflector::explode($method);

            if ($parts[0] == 'is' && isset($parts[1])) {
                return false;
            }
        }

        return parent::__call($method, $args);
    }
}
