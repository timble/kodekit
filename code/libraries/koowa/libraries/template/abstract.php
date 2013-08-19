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
  *
  * @method KCommandContext getCommandContext() Get the command context
  * @method KCommandChain   getCommandChain() Get the command chain
  */
abstract class KTemplateAbstract extends KObject implements KTemplateInterface
{
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
     * The set of template filters for templates
     *
     * @var array
     */
    protected $_filters;

    /**
     * View object or identifier
     *
     * @var    string|object
     */
    protected $_view;

    /**
     * Counter
     *
     * Used to track recursive calls during template evaluation
     *
     * @var int
     * @see _evaluate()
     */
    private $__counter;

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

		 // Mixin a command chain
        $this->mixin(new KCommandMixin($config->append(array('mixer' => $this))));

        //Attach the filters
        $filters = (array) KObjectConfig::unbox($config->filters);

        foreach ($filters as $key => $value)
        {
            if (is_numeric($key)) {
                $this->addFilter($value);
            } else {
                $this->addFilter($key, $value);
            }
        }

        $this->setTranslator($config->translator);

        //Reset the counter
        $this->__counter = 0;
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
            'translator'       => null,
            'data'             => array(),
            'filters'          => array(),
            'view'             => null,
            'command_chain' 	=> $this->getService('koowa:command.chain'),
    		'dispatch_events'   => false,
    		'enable_callbacks' 	=> false,
        ));

        parent::_initialize($config);
    }

	/**
	 * Get the template path
	 *
	 * @return	string
	 */
	public function getPath()
	{
		return $this->_path;
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
     * Get the template content
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Get the view object attached to the template
     *
     * @throws	\UnexpectedValueException	If the views doesn't implement the KViewInterface
     * @return  KTemplateInterface
     */
    public function getView()
    {
        if(!$this->_view instanceof KViewAbstract)
        {
            //Make sure we have a view identifier
            if(!($this->_view instanceof KServiceIdentifier)) {
                $this->setView($this->_view);
            }

            $this->_view = $this->getService($this->_view);

            //Make sure the view implements KViewAbstract
            if(!$this->_view instanceof KViewAbstract)
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
	 * @param	mixed	$view An object that implements KObjectInterface, KServiceIdentifier object
	 * 					or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a view identifier
	 * @return	KTemplateAbstract
	 */
	public function setView($view)
	{
		if(!($view instanceof KViewAbstract))
		{
			if(empty($view) || (is_string($view) && strpos($view, '.') === false))
		    {
			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('view');
			    if ($view) {
			        $identifier->path[] = $view;
			    }
			    $identifier->name	= KRequest::format() ? KRequest::format() : 'html';
			}
			else $identifier = $this->getIdentifier($view);

			if($identifier->path[0] != 'view') {
				throw new UnexpectedValueException('Identifier: '.$identifier.' is not a view identifier');
			}

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
                $identifier = clone $this->getIdentifier();
                $identifier->path = array();
                $identifier->name = 'translator';
            } else $identifier = $this->getIdentifier($translator);

            $translator = $this->getService($identifier);
        }

        $this->_translator = $translator;

        return $this;
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
	 * Load a template by identifier
	 *
	 * This functions only accepts full identifiers of the format
	 * -  com:[//application/]component.view.[.path].name
	 *
	 * @param   string 	$template   The template identifier
	 * @param	array	$data       An associative array of data to be extracted in local template scope
     * @throws \InvalidArgumentException If the template could not be found
	 * @return KTemplateAbstract
	 */
	public function loadIdentifier($template, $data = array())
	{
	    //Identify the template
	    $identifier = $this->getIdentifier($template);

	    // Find the template
		$file = $this->findFile(dirname($identifier->filepath).'/'.$identifier->name.'.php');

		if ($file === false) {
			throw new InvalidArgumentException('Template "' . $identifier->name . '" not found');
		}

		// Load the file
		$this->loadFile($file, $data);

		return $this;
	}

	/**
     * Load a template by path
     *
     * @param   string  $path     The template path
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @return KTemplateAbstract
     */
    public function loadFile($path, $data = array())
    {
        //Store the original path
        $this->_path = $path;

        //Get the file contents
        $contents = file_get_contents($path);

        //Load the contents
        $this->loadString($contents, $data);
        
		return $this;
	}

    /**
     * Load a template from a string
     *
     * @param  string   $string     The template contents
     * @param  array    $data       An associative array of data to be extracted in local template scope
     * @return KTemplateAbstract
     */
	public function loadString($string, $data = array())
	{
		$this->_content = $string;

		// Merge the data
	    $this->_data = array_merge((array) $this->_data, $data);

        // Process inline templates
        if($this->__counter > 0) {
            $this->render();
        }

		return $this;
	}

    /**
     * Render the template
     *
     * @return string  The rendered data
     */
    public function render()
    {
        //Parse the template
        $this->_parse($this->_content);

        //Evaluate the template
        $this->_evaluate($this->_content);

        //Process the template only at the end of the render cycle.
        if($this->__counter == 0) {
            $this->_process($this->_content);
        }

        return $this->_content;
    }

    /**
     * Check if the template is in a render cycle
     *
     * @return boolean Return TRUE if the template is being rendered
     */
    public function isRendering()
    {
        return (bool) $this->_counter;
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
     * Attach ar filters for template transformation
     *
     * @param   mixed  $filter An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param   array $config  An optional associative array of configuration settings
     * @return KTemplateAbstract
     */
	public function addFilter($filter, $config = array())
 	{
 	    if(!($filter instanceof KTemplateFilterInterface)) {
			$filter = $this->getFilter($filter, $config);
		}

		//Enqueue the filter in the command chain
		$this->getCommandChain()->enqueue($filter);

		return $this;
 	}

    /**
     * Get a filter by identifier
     *
     * @param   mixed    $filter    An object that implements ObjectInterface, ObjectIdentifier object
     *                              or valid identifier string
     * @param   array    $config    An optional associative array of configuration settings
     * @return KTemplateFilterInterface
     */
 	 public function getFilter($filter, $config = array())
 	 {
         //Create the complete identifier if a partial identifier was passed
        if(is_string($filter) && strpos($filter, '.') === false )
        {
            $identifier = clone $this->getIdentifier();
            $identifier->path = array('template', 'filter');
            $identifier->name = $filter;
        }
        else $identifier = KService::getIdentifier($filter);

        if (!isset($this->_filters[$identifier->name]))
        {
            $filter = $this->getService($identifier, array_merge($config, array('template' => $this)));

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
     * Get a template helper
     *
     * @param mixed $helper KServiceIdentifierInterface
     * @throws UnexpectedValueException
     * @return KTemplateHelperInterface
     */
	public function getHelper($helper)
	{
		//Create the complete identifier if a partial identifier was passed
		if(is_string($helper) && strpos($helper, '.') === false )
		{
            $identifier = clone $this->getIdentifier();
            $identifier->path = array('template','helper');
            $identifier->name = $helper;
		}
		else $identifier = $this->getIdentifier($helper);

		//Create the template helper
		$helper = $this->getService($identifier, array('template' => $this));

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
		//Get the function to call based on the $identifier
		$parts    = explode('.', $identifier);
		$function = array_pop($parts);

		$helper = $this->getHelper(implode('.', $parts));

		//Call the helper function
		if (!is_callable( array( $helper, $function ) )) {
			throw new BadMethodCallException( get_class($helper).'::'.$function.' not supported.' );
		}

		return $helper->$function($params);
	}

	/**
	 * Searches for the file
	 *
	 * @param	string	$file The file path to look for.
	 * @return	mixed	The full path and file name for the target file, or FALSE
	 * 					if the file is not found
	 */
	public function findFile($file)
	{
        $result = false;
        $path = dirname($file);

        // is the path based on a stream?
        if (strpos($path, '://') === false)
        {
            // not a stream, so do a realpath() to avoid directory
            // traversal attempts on the local file system.
            $path = realpath($path); // needed for substr() later
            $file = realpath($file);
        }

        // The substr() check added to make sure that the realpath()
        // results in a directory registered so that non-registered directores
        // are not accessible via directory traversal attempts.
        if (file_exists($file) && substr($file, 0, strlen($path)) == $path) {
            $result = $file;
        }

        // could not find the file in the set of paths
        return $result;
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
     * Parse and compile the template to PHP code
     *
     * This function passes the template through read filter chain and returns the result.
     *
     * @param  string $content Data to parse
     */
    protected function _parse(&$content)
    {
        $context = $this->getCommandContext();

        $context->data = $content;
        $this->getCommandChain()->run(KTemplateFilter::MODE_READ, $context);
        $content = $context->data;
    }

    /**
     * Evaluate the template using a simple sandbox
     *
     * This function writes the template to a temporary file and then includes it.
     *
     * @param string $content The evaluated data
     * @see tempnam()
     */
    protected function _evaluate(&$content)
    {
        //Increase counter
        $this->__counter++;

        //Create temporary file
        $tempfile = $this->_getTemporaryFile();

        //Write the template to the file
        $handle = fopen($tempfile, "w+");
        fwrite($handle, $content);
        fclose($handle);

        //Include the file
        extract($this->_data, EXTR_SKIP);

        ob_start();
        include $tempfile;
        $content = ob_get_clean();

        unlink($tempfile);

        //Reduce counter
        $this->__counter--;
    }

    /**
     * Process the template
     *
     * This function passes the template through write filter chain and returns the result.
     *
     * @param string $content Data to render
     */
    protected function _process(&$content)
    {
        $context = $this->getCommandContext();

        $context->data = $content;
        $this->getCommandChain()->run(KTemplateFilter::MODE_WRITE, $context);
        $content = $context->data;
    }

    /**
     * Returns the template contents
     *
     * @return  string
     * @see getContents()
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
