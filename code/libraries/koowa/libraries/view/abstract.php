<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
abstract class KViewAbstract extends KObject implements KViewInterface, KCommandCallbackDelegate
{
    /**
     * Model identifier (com://APP/COMPONENT.model.NAME)
     *
     * @var	string|object
     */
    protected $_model;

    /**
     * The uniform resource locator
     *
     * @var KHttpUrl
     */
    protected $_url;

    /**
     * The content of the view
     *
     * @var string
     */
    protected $_content;

    /**
     * The view data
     *
     * @var boolean
     */
    protected $_data;

    /**
     * The mimetype
     *
     * @var string
     */
    public $mimetype = '';

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the data
        $this->_data = KObjectConfig::unbox($config->data);

        $this->setUrl($config->url);
        $this->setContent($config->content);
        $this->mimetype = $config->mimetype;

        $this->setModel($config->model);

        // Mixin the behavior (and command) interface
        $this->mixin('lib:behavior.mixin', $config);

        // Mixin the event interface
        $this->mixin('lib:event.mixin', $config);

        //Fetch the view data before rendering
        $this->addCommandCallback('before.render', '_fetchData');

        //Fetch the view data before rendering
        $this->addCommandCallback('before.render', '_loadTranslations');
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
            'data'              => array(),
            'command_chain'     => 'lib:command.chain',
            'command_handlers'  => array('lib:command.handler.event'),
            'model'      => 'lib:model.empty',
            'content'	 => '',
            'mimetype'	 => '',
            'url'        =>  $this->getObject('lib:http.url')
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
        $context->data       = array_merge($this->getData(), $data);
        $context->action     = 'render';
        $context->parameters = array();

        if ($this->invokeCommand('before.render', $context) !== false)
        {
            //Render the view
            $context->result = $this->_actionRender($context);
            $this->invokeCommand('after.render', $context);
        }

        return $context->result;
    }

    /**
     * Invoke a command handler
     *
     * @param string             $method    The name of the method to be executed
     * @param KCommandInterface  $command   The command
     * @return mixed Return the result of the handler.
     */
    public function invokeCommandCallback($method, KCommandInterface $command)
    {
        return $this->$method($command);
    }

    /**
     * Render the view
     *
     * @param KViewContext	$context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
        $contents = $this->getContent();
        return trim($contents);
    }

    /**
     * Fetch the view data
     *
     * @param KViewContext	$context A view context object
     * @return void
     */
    protected function _fetchData(KViewContext $context)
    {

    }

    /**
     * Load the view translations
     *
     * @param KViewContext  $context A view context object
     * @return void
     */
    protected function _loadTranslations(KViewContext $context)
    {
        $package = $this->getIdentifier()->package;
        $domain  = $this->getIdentifier()->domain;

        if($domain) {
            $identifier = 'com://'.$domain.'/'.$package;
        } else {
            $identifier = 'com:'.$package;
        }

        $this->getObject('translator')->load($identifier);
    }

    /**
     * Set a view property
     *
     * @param   string $property The property name.
     * @param   mixed  $value    The property value.
     * @return KViewAbstract
     */
    public function set($property, $value)
    {
        $this->_data[$property] = $value;
        return $this;
    }

    /**
     * Get a view property
     *
     * @param  string $property The property name.
     * @param  mixed  $default  Default value to return.
     * @throws InvalidArgumentException
     * @return string  The property value.
     */
    public function get($property, $default = null)
    {
        return isset($this->_data[$property]) ? $this->_data[$property] : $default;
    }

    /**
     * Check if a view property exists
     *
     * @param   string  $property   The property name.
     * @return  boolean TRUE if the property exists, FALSE otherwise
     */
    public function has($property)
    {
        return isset($this->_data[$property]);
    }

    /**
     * Sets the view data
     *
     * @param   array $data The view data
     * @return  KViewAbstract
     */
    public function setData($data)
    {
        foreach($data as $name => $value) {
            $this->set($name, $value);
        }

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
     * Get the name
     *
     * @return 	string 	The name of the object
     */
    public function getName()
    {
        $total = count($this->getIdentifier()->path);
        return $this->getIdentifier()->path[$total - 1];
    }

    /**
     * Get the title
     *
     * @return  string  The title of the view
     */
    public function getTitle()
    {
        return ucfirst($this->getName());
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
     * Get the content
     *
     * @return  string The content of the view
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Get the contents
     *
     * @param  string $content The contents of the view
     * @return KViewAbstract
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Get the model object attached to the view
     *
     * @throws  UnexpectedValueException    If the model doesn't implement the ModelInterface
     * @return  KModelInterface
     */
    public function getModel()
    {
        if(!$this->_model instanceof KModelInterface)
        {
            if(!($this->_model instanceof KObjectIdentifier)) {
                $this->setModel($this->_model);
            }

            $this->_model = $this->getObject($this->_model);

            if(!$this->_model instanceof KModelInterface)
            {
                throw new UnexpectedValueException(
                    'Model: '.get_class($this->_model).' does not implement KModelInterface'
                );
            }
        }

        return $this->_model;
    }

    /**
     * Method to set a model object attached to the controller
     *
     * @param   mixed   $model An object that implements KObjectInterface, KObjectIdentifier object
     *                         or valid identifier string
     * @return	KViewAbstract
     */
    public function setModel($model)
    {
        if(!($model instanceof KModelInterface))
        {
            if(is_string($model) && strpos($model, '.') === false )
            {
                // Model names are always plural
                if(KStringInflector::isSingular($model)) {
                    $model = KStringInflector::pluralize($model);
                }

                $identifier			= $this->getIdentifier()->toArray();
                $identifier['path']	= array('model');
                $identifier['name'] = $model;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($model);

            $model = $identifier;
        }

        $this->_model = $model;

        return $this;
    }

    /**
     * Get the view url
     *
     * @return  KHttpUrl  A HttpUrl object
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set the view url
     *
     * @param KHttpUrl $url   A KHttpUrl object or a string
     * @return  KViewAbstract
     */
    public function setUrl(KHttpUrl $url)
    {
        //Remove the user and pass from the view url
        unset($url->user);
        unset($url->pass);

        $this->_url = $url;
        return $this;
    }

    /**
     * Get a route based on a full or partial query string
     *
     * 'option', 'view' and 'layout' can be omitted. The following variations will all result in the same route :
     *
     * - foo=bar
     * - component=[package]&view=[name]&foo=bar
     *
     * In templates, use route()
     *
     * @param   string|array $route  The query string or array used to create the route
     * @param   boolean      $fqr    If TRUE create a fully qualified route. Defaults to TRUE.
     * @param   boolean      $escape If TRUE escapes the route for xml compliance. Defaults to TRUE.
     * @return  KDispatcherRouterRoute The route
     */
    public function getRoute($route = '', $fqr = true, $escape = true)
    {
        //Parse route
        $parts = array();

        if(is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        //Check to see if there is component information in the route if not add it
        if (!isset($parts['component'])) {
            $parts['component'] = $this->getIdentifier()->package;
        }

        //Add the view information to the route if it's not set
        if (!isset($parts['view'])) {
            $parts['view'] = $this->getName();
        }

        //Add the format information to the route only if it's not 'html'
        if (!isset($parts['format']) && $this->getIdentifier()->name !== 'html') {
            $parts['format'] = $this->getIdentifier()->name;
        }

        //Add the model state only for routes to the same view
        if ($parts['component'] == $this->getIdentifier()->package && $parts['view'] == $this->getName())
        {
            $states = array();
            foreach($this->getModel()->getState() as $name => $state)
            {
                if($state->default != $state->value && !$state->internal) {
                    $states[$name] = $state->value;
                }
            }

            $parts = array_merge($states, $parts);
        }

        //Create the route
        $route = $this->getObject('lib:dispatcher.router.route', array('escape' =>  $escape))
                      ->setQuery($parts);

        //Add the host and the schema
        if ($fqr === true)
        {
            $route->scheme = $this->getUrl()->scheme;
            $route->host   = $this->getUrl()->host;
        }

        return $route;
    }

    /**
     * Get the view context
     *
     * @return  KViewContext
     */
    public function getContext()
    {
        $context = new KViewContext();
        $context->setSubject($this);
        $context->setData($this->_data);

        return $context;
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
        return KStringInflector::isPlural($this->getName());
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
            trigger_error('KViewAbstract::__toString exception: '. (string) $e, E_USER_ERROR);
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
     * @return  KViewAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        if (!isset($this->_mixed_methods[$method]))
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
            $parts = KStringInflector::explode($method);

            if ($parts[0] == 'is' && isset($parts[1])) {
                return false;
            }
        }

        return parent::__call($method, $args);
    }
}