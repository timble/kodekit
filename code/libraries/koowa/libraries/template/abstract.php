<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

 /**
  * Abstract Template
  *
  * @author  Johan Janssens <https://github.com/johanjanssens>
  * @package Koowa\Library\Template\Abstract
  */
abstract class KTemplateAbstract extends KObject implements KTemplateInterface
{
    /**
     * Tracks the status the template
     *
     * Available template status values are defined as STATUS_ constants
     *
     * @var string
     */
    protected $_status = null;

    /**
     * The template path
     *
     * @var string
     */
    protected $_path;

    /**
     * The template data
     *
     * @var array
     */
    protected $_data;

    /**
     * The template contents
     *
     * @var string
     */
    protected $_content;

    /**
     * View object or identifier
     *
     * @var    string|object
     */
    protected $_view;

    /**
     * Template stack
     *
     * Used to track recursive load calls during template evaluation
     *
     * @var array
     * @see load()
     */
    protected $_stack;

    /**
     * List of template filters
     *
     * @var array
     */
    protected $_filters;

    /**
     * Filter queue
     *
     * @var	KObjectQueue
     */
    protected $_queue;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     *
     * @param KObjectConfig $config   An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Set the view identifier
        $this->_view = $config->view;

        // Set the template data
        $this->_data = $config->data;

        //Set the filter queue
        $this->_queue = $this->getObject('lib:object.queue');

        //Register the loaders
        $this->_locators = KObjectConfig::unbox($config->locators);

        //Attach the filters
        $filters = KObjectConfig::unbox($config->filters);

        foreach ($filters as $key => $value)
        {
            if (is_numeric($key)) {
                $this->attachFilter($value);
            } else {
                $this->attachFilter($key, $value);
            }
        }

        //Reset the stack
        $this->_stack = array();
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config  An optional KObjectConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'data'       => array(),
            'view'       => null,
            'filters'    => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Load a template by path
     *
     * @param   string  $url      The template url
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @param   integer $status   The template state
     * @throws  InvalidArgumentException If the template could not be found
     * @return KTemplateAbstract
     */
    public function load($url, $data = array(), $status = self::STATUS_LOADED)
    {
        //Get the template locator
        $locator = $this->getObject('template.locator.factory')->createLocator($url, $this->getPath());

        //Check of the file exists
        if (!$file = $locator->locate($url, $this->getPath())) {
            throw new InvalidArgumentException('Template "' . $url . '" not found');
        }

        //Push the path on the stack
        array_push($this->_stack, $url);

        //Set the status
        $this->_status = $status;

        //Load the file content
        $this->_content = file_get_contents($file);

        //Compile and evaluate partial templates
        if(count($this->_stack) > 1)
        {
            if(!($status & self::STATUS_COMPILED)) {
                $this->compile();
            }

            if(!($status & self::STATUS_EVALUATED)) {
                $this->evaluate($data);
            }
        }

        return $this;
    }

    /**
     * Parse and compile the template to PHP code
     *
     * This function passes the template through compile filter queue and returns the result.
     *
     * @return KTemplateInterface
     */
    public function compile()
    {
        if(!($this->_status & self::STATUS_COMPILED))
        {
            foreach($this->_queue as $filter)
            {
                if($filter instanceof KTemplateFilterCompiler) {
                    $filter->compile($this->_content);
                }
            }

            //Set the status
            $this->_status ^= self::STATUS_COMPILED;
        }

        return $this;
    }

    /**
     * Evaluate the template using a simple sandbox
     *
     * This function writes the template to a temporary file and then includes it.
     *
     * @param  array   $data  An associative array of data to be extracted in local template scope
     * @return KTemplateAbstract
     * @see tempnam()
     */
    public function evaluate($data = array())
    {
        if(!($this->_status & self::STATUS_EVALUATED))
        {
            //Merge the data
            $this->_data = array_merge((array) $this->_data, $data);

            //Write the template to a temp file
            $stream = $this->getObject('filesystem.stream.factory')->createStream('buffer://temp', 'w+b');
            $stream->write($this->_content);

            //Include the file
            extract($this->_data, EXTR_SKIP);

            ob_start();
            include $stream->getPath();
            $this->_content = ob_get_clean();

            $stream->close();

            //Remove the path from the stack
            array_pop($this->_stack);

            //Set the status
            $this->_status ^= self::STATUS_EVALUATED;
        }

        return $this;
    }

    /**
     * Process the template
     *
     * This function passes the template through the render filter queue
     *
     * @return KTemplateInterface
     */
    public function render()
    {
        if(!($this->_status & self::STATUS_RENDERED))
        {
            foreach($this->_queue as $filter)
            {
                if($filter instanceof KTemplateFilterRenderer) {
                    $filter->render($this->_content);
                }
            }

            //Set the status
            $this->_status ^= self::STATUS_RENDERED;
        }

        return $this;
    }

    /**
     * Escape a string
     *
     * By default the function uses htmlspecialchars to escape the string
     *
     * @param string $string String to to be escape
     * @return string Escaped string
     */
    public function escape($string)
    {
        if(is_string($string)) {
            $string = htmlspecialchars($string, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8', false);
        }

        return $string;
    }

    /**
     * Get the template path
     *
     * @return	string
     */
    public function getPath()
    {
        return end($this->_stack);
    }

    /**
     * Get the format
     *
     * @return 	string 	The format of the view
     */
    public function getFormat()
    {
        return $this->getView()->getFormat();
    }

    /**
     * Get the template data
     *
     * @return	mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Set the template data
     *
     * @param  array   $data     The template data
     * @return KTemplateInterface
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Get the template content
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Set the template content from a string
     *
     * @param  string   $content    The template content
     * @param  integer  $status     The template state
     * @return KTemplateInterface
     */
    public function setContent($content, $status = self::STATUS_LOADED)
    {
        $this->_content = $content;
        $this->_status  = $status;

        return $this;
    }

    /**
     * Get the view object attached to the template
     *
     * @throws	UnexpectedValueException	If the views doesn't implement the ViewInterface
     * @return  KViewInterface
     */
    public function getView()
    {
        if(!$this->_view instanceof KViewInterface)
        {
            //Make sure we have a view identifier
            if(!($this->_view instanceof KObjectIdentifier)) {
                $this->setView($this->_view);
            }

            $this->_view = $this->getObject($this->_view);

            //Make sure the view implements ViewInterface
            if(!$this->_view instanceof KViewInterface)
            {
                throw new UnexpectedValueException(
                    'View: '.get_class($this->_view).' does not implement KViewInterface'
                );
            }
        }

        return $this->_view;
    }

    /**
     * Method to set a view object attached to the controller
     *
     * @param	mixed	$view An object that implements ObjectInterface, ObjectIdentifier object
     * 					      or valid identifier string
     * @return KTemplateInterface
     */
    public function setView($view)
    {
        if(!($view instanceof KViewInterface))
        {
            if(is_null($view) || (is_string($view) && strpos($view, '.') === false))
            {
                $identifier			= $this->getIdentifier()->toArray();
                $identifier['path']	= array('view');

                if ($view) {
                    $identifier['path'][] = $view;
                }

                $identifier['name'] = 'html';

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($view);

            $view = $identifier;
        }

        $this->_view = $view;

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
        return isset($this->_filters[$filter]);
    }

    /**
     * Get a filter by identifier
     *
     * @param   mixed $filter       An object that implements KObjectInterface, KObjectIdentifier object
     *                              or valid identifier string
     * @param   array $config       An optional associative array of configuration settings
     *
     * @throws UnexpectedValueException
     * @return KTemplateFilterInterface
     */
     public function getFilter($filter, $config = array())
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
            $filter = $this->getObject($identifier, array_merge($config, array('template' => $this)));

            if (!($filter instanceof KTemplateFilterInterface))
            {
                throw new UnexpectedValueException(
                    "Template filter $identifier does not implement KTemplateFilterInterface"
                );
            }

            $this->_filters[$filter->getIdentifier()->name] = $filter;
        }
        else $filter = $this->_filters[$identifier->name];

        return $filter;
     }

    /**
     * Attach a filter for template transformation
     *
     * @param   mixed  $filter An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param   array $config  An optional associative array of configuration settings
     * @return KTemplateAbstract
     */
    public function attachFilter($filter, $config = array())
    {
        if(!($filter instanceof KTemplateFilterInterface)) {
            $filter = $this->getFilter($filter, $config);
        }

        //Enqueue the filter
        $this->_queue->enqueue($filter, $filter->getPriority());

        return $this;
    }

    /**
     * Get a template helper
     *
     * @param    mixed $helper KObjectIdentifierInterface
     * @param    array $config An optional associative array of configuration settings
     *
     * @throws \UnexpectedValueException
     * @return  KTemplateHelperInterface
     */
    public function getHelper($helper, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($helper) && strpos($helper, '.') === false)
        {
            $identifier = $this->getIdentifier()->toArray();
            $identifier['path'] = array('template','helper');
            $identifier['name'] = $helper;
        }
        else $identifier = $this->getIdentifier($helper);

        //Create the template helper
        $helper = $this->getObject($identifier, array_merge($config, array('template' => $this)));

        //Check the helper interface
        if (!($helper instanceof KTemplateHelperInterface)) {
            throw new UnexpectedValueException("Template helper $identifier does not implement KTemplateHelperInterface");
        }

        return $helper;
    }

    /**
     * Invoke a template helper method
     *
     * This function accepts a partial identifier, in the form of helper.method or schema:package.helper.method. If
     * a partial identifier is passed a full identifier will be created using the template identifier.
     *
     * If the view state have the same string keys, then the parameter value for that key will overwrite the state.
     *
     * @param    string   $identifier Name of the helper, dot separated including the helper function to call
     * @param    array    $params     An optional associative array of functions parameters to be passed to the helper
     * @return   string   Helper output
     * @throws   BadMethodCallException If the helper function cannot be called.
     */
    public function invokeHelper($identifier, $params = array())
    {
        //Get the function and helper based on the identifier
        $parts      = explode('.', $identifier);
        $function   = array_pop($parts);
        $identifier = array_pop($parts);

        //Handle schema:package.helper.function identifiers
        if(!empty($parts)) {
            $identifier = implode('.', $parts).'.template.helper.'.$identifier;
        }

        $helper = $this->getHelper($identifier, $params);

        //Call the helper function
        if (!is_callable(array($helper, $function))) {
            throw new BadMethodCallException(get_class($helper) . '::' . $function . ' not supported.');
        }

        //Merge the view state with the helper params
        $view = $this->getView();

        if (KStringInflector::isPlural($view->getName()))
        {
            if ($state = $view->getModel()->getState()) {
                $params = array_merge($state->getValues(), $params);
            }
        }
        else
        {
            if($entity = $view->getModel()->fetch()) {
                $params = array_merge($entity->getProperties(), $params);
            }
        }

        return $helper->$function($params);
    }

    /**
     * Check if the template is loaded
     *
     * @return boolean  Returns TRUE if the template is loaded. FALSE otherwise
     */
    public function isLoaded()
    {
        return $this->_status & self::STATUS_LOADED;
    }

    /**
     * Check if the template is compiled
     *
     * @return boolean  Returns TRUE if the template is compiled. FALSE otherwise
     */
    public function isCompiled()
    {
        return $this->_status & self::STATUS_COMPILED;
    }

    /**
     * Check if the template is evaluated
     *
     * @return boolean  Returns TRUE if the template is evaluated. FALSE otherwise
     */
    public function isEvaluated()
    {
        return $this->_status & self::STATUS_EVALUATED;
    }

    /**
     * Check if the template is rendered
     *
     * @return boolean  Returns TRUE if the template is rendered. FALSE otherwise
     */
    public function isRendered()
    {
        return $this->_status & self::STATUS_RENDERED;
    }

    /**
     * Returns the template contents
     *
     * When casting to a string the template content will be compiled, evaluated and rendered.
     *
     * @return  string
     */
    public function toString()
    {
        return $this->getContent();
    }

    /**
     * Cast the object to a string
     *
     * @return  string
     */
    final public function __toString()
    {
        return $this->toString();
    }
}
