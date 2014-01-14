<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

 /**
  * Abstract Template
  *
  * @author  Johan Janssens <https://github.com/johanjanssens>
  * @package Koowa\Library\Template
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
     * Translator object
     *
     * @var	KTranslator
     */
    protected $_translator;

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
     * The template locators
     *
     * @var array
     */
    protected $_locators;

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
     * The set of template filters for templates
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
        $this->_queue = $this->getObject('koowa:object.queue');

        //Register the loaders
        $this->_locators = KObjectConfig::unbox($config->locators);

        //Attach the filters
        $filters = (array) KObjectConfig::unbox($config->filters);

        foreach ($filters as $key => $value)
        {
            if (is_numeric($key)) {
                $this->attachFilter($value);
            } else {
                $this->attachFilter($key, $value);
            }
        }

        $this->setTranslator($config->translator);

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
            'translator' => null,
            'data'       => array(),
            'view'       => null,
            'filters'    => array(),
            'locators' => array('com' => 'koowa:template.locator.component')
        ));

        parent::_initialize($config);
    }

    /**
     * Load a template by path
     *
     * @param   string  $path     The template path
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @param   integer $status   The template state
     * @throws \InvalidArgumentException If the template could not be found
     * @return $this
     */
    public function load($path, $data = array(), $status = self::STATUS_LOADED)
    {
        $parts   = parse_url($path);

        //Set the default type if no scheme can be found
        $locator = $this->getLocator(isset($parts['scheme']) ? $parts['scheme'] : $this->getIdentifier()->type);

        if (!$locator) {
            $locator = $this->getLocator('com');
        }

        //Check of the file exists
        if (!$template = $locator->locate($path)) {
            throw new InvalidArgumentException('Template "' . $path . '" not found');
        }

        //Push the path on the stack
        array_push($this->_stack, $path);

        //Set the status
        $this->_status = $status;

        //Load the file
        $this->_content = file_get_contents($template);

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
     * @return $this
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
     * @return $this
     * @see tempnam()
     */
    public function evaluate($data = array())
    {
        if(!($this->_status & self::STATUS_EVALUATED))
        {
            //Merge the data
            $this->_data = array_merge((array) $this->_data, $data);

            //Create temporary file
            $tempfile = $this->_getTemporaryFile();

            //Write the template to the file
            $handle = fopen($tempfile, "w+");
            fwrite($handle, $this->_content);
            fclose($handle);

            //Include the file
            extract($this->_data, EXTR_SKIP);

            ob_start();
            include $tempfile;
            $this->_content = ob_get_clean();

            unlink($tempfile);

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
     * @return $this
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
        return htmlspecialchars($string);
    }

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     *
     * @return string Translated string
     */
    public function translate($string, array $parameters = array())
    {
        return $this->getTranslator()->translate($string, $parameters);
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
     * @return $this
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
     * @return $this
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
     * @return KTemplateAbstract
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
     * Gets the translator object
     *
     * @return  KTranslator
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * Sets the translator object
     *
     * @param string|KTranslator $translator A translator object or identifier
     * @return $this
     */
    public function setTranslator($translator)
    {
        if (!$translator instanceof KTranslator)
        {
            if (empty($translator) || (is_string($translator) && strpos($translator, '.') === false && $translator !== 'translator'))
            {
                $identifier = $this->getIdentifier()->toArray();
                $identifier['path'] = array();
                $identifier['name'] = 'translator';
            }
            else $identifier = $this->getIdentifier($translator);

            $translator = $this->getObject($identifier);
        }

        $this->_translator = $translator;

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
     * @param   mixed $filter    An object that implements ObjectInterface, ObjectIdentifier object
     *                              or valid identifier string
     * @param   array $config    An optional associative array of configuration settings
     * @throws UnexpectedValueException
     * @return KTemplateFilterInterface
     */
 	 public function getFilter($filter, $config = array())
 	 {
         //Create the complete identifier if a partial identifier was passed
        if(is_string($filter) && strpos($filter, '.') === false )
        {
            $identifier = $this->getIdentifier()->toArray();
            $identifier['path'] = array('template', 'filter');
            $identifier['name'] = $filter;

            $identifier = $this->getIdentifier($identifier);
        }
        else $identifier = $this->getIdentifier($filter);

        if (!isset($this->_filters[$identifier->name]))
        {
            $filter = $this->getObject($identifier, array_merge($config, array('template' => $this)));

            if(!($filter instanceof KTemplateFilterInterface))
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
     * Attach ar filters for template transformation
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
     * @param    mixed $helper ObjectIdentifierInterface
     * @param    array $config An optional associative array of configuration settings
     * @throws UnexpectedValueException
     * @return  KTemplateHelperInterface
     */
    public function getHelper($helper, $config = array())
	{
		//Create the complete identifier if a partial identifier was passed
		if(is_string($helper) && strpos($helper, '.') === false )
		{
            $identifier = $this->getIdentifier()->toArray();
            $identifier['path'] = array('template','helper');
            $identifier['name'] = $helper;
		}
		else $identifier = $this->getIdentifier($helper);

		//Create the template helper
        $helper = $this->getObject($identifier, array_merge($config, array('template' => $this)));

	    //Check the helper interface
        if(!($helper instanceof KTemplateHelperInterface)) {
            throw new UnexpectedValueException("Template helper $identifier does not implement KTemplateHelperInterface");
        }

		return $helper;
	}

	/**
	 * Load a template helper
	 *
	 * This functions accepts a partial identifier, in the form of helper.function. If a partial
	 * identifier is passed a full identifier will be created using the template identifier.
	 *
	 * @param	string	$identifier Name of the helper, dot separated including the helper function to call
	 * @param	mixed	$params     Parameters to be passed to the helper
     * @throws BadMethodCallException
	 * @return 	string	Helper output
	 */
	public function renderHelper($identifier, $params = array())
	{
        //Get the function and helper based on the identifier
        $parts    = explode('.', $identifier);
        $function = array_pop($parts);

        $helper = $this->getHelper(implode('.', $parts), $params);

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
            if ($item = $view->getModel()->getItem()) {
                $params = array_merge($item->getData(), $params);
            }
        }

        return $helper->$function($params);
	}

    /**
     * Register a template locator
     *
     * @param KTemplateLocatorInterface $locator
     * @return $this
     */
    public function registerLocator(KTemplateLocatorInterface $locator)
    {
        $this->_locators[$locator->getType()] = $locator;
        return $this;
    }

    /**
     * Get a registered template locator based on his type
     *
     * @param string $type
     * @param array  $config
     * @throws UnexpectedValueException
     * @return KTemplateLocatorInterface|null  Returns the template loader or NULL if the loader can not be found.
     */
    public function getLocator($type, $config = array())
    {
        $locator = null;
        if(isset($this->_locators[$type]))
        {
            $locator = $this->_locators[$type];

            if(!$locator instanceof KTemplateLocatorInterface)
            {
                //Create the complete identifier if a partial identifier was passed
                if (is_string($locator) && strpos($locator, '.') === false)
                {
                    $identifier = $this->getIdentifier()->toArray();
                    $identifier['path'] = array('template', 'locator');
                    $identifier['name'] = $locator;
                }
                else $identifier = $this->getIdentifier($locator);

                $locator = $this->getObject($identifier, array_merge($config, array('template' => $this)));

                if (!($locator instanceof KTemplateLocatorInterface))
                {
                    throw new UnexpectedValueException(
                        "Template loader $identifier does not implement KTemplateLocatorInterface"
                    );
                }

                $this->_locators[$type] = $locator;
            }
        }

        return $locator;
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
     * Returns a directory path for temporary files
     *
     * @return string Folder path
     */
    protected function _getTemporaryDirectory()
    {
        return sys_get_temp_dir();
    }

    /**
     * Creates a file with a unique file name
     *
     * @param string|null $directory Uses the result of _getTemporaryDirectory() by default
     * @return string File path
     */
    protected function _getTemporaryFile($directory = null)
    {
        if ($directory === null) {
            $directory = $this->_getTemporaryDirectory();
        }

        $name = str_replace('.', '', uniqid('tmpl', true));
        $path = $directory.'/'.$name;

        touch($path);

        return $path;
    }

    /**
     * Returns the template contents
     *
     * When casting to a string the template content will be compiled, evaluated and rendered.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
