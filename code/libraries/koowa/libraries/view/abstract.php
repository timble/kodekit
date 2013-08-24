<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
abstract class KViewAbstract extends KObject implements KViewInterface
{
    /**
     * Translator object
     *
     * @var	KTranslator
     */
    protected $_translator;

	/**
	 * Model identifier (com://APP/COMPONENT.model.NAME)
	 *
	 * @var	string|object
	 */
	protected $_model;

    /**
     * The content of the view
     *
     * @var string
     */
    protected $_content;

    /**
     * Layout name
     *
     * @var     string
     */
    protected $_layout;

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
	public function __construct(KObjectConfig $config = null)
	{
		//If no config is passed create it
		if(!isset($config)) $config = new KObjectConfig();

		parent::__construct($config);

		//Set the output if defined in the config
        $this->setContent($config->content);

		//Set the mimetype of defined in the config
		$this->mimetype = $config->mimetype;

		// set the model
		$this->setModel($config->model);
        $this->setTranslator($config->translator);

		// set the layout
        $this->setLayout($config->layout);
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
            'model'      => 'koowa:model.empty',
            'translator' => null,
	    	'content'	 => '',
    		'mimetype'	 => '',
            'layout'     => 'default',
	  	));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * @return string 	The  of the view
     */
    public function display()
    {
        $content = $this->getContent();
        return trim($content);
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
     * Set a view property
     *
     * @param   string  $property The property name.
     * @param   mixed   $value    The property value.
     * @return KViewAbstract
     */
    /*public function set($property, $value)
    {
        $this->$property = $value;
        return $this;
    }*/

    /**
     * Get a view property
     *
     * @param   string  $property The property name.
     * @return  string  The property value.
     */
    /*public function get($property)
    {
        return isset($this->$property) ? $this->$property : null;
    }*/

    /**
     * Check if a view property exists
     *
     * @param   string  $property   The property name.
     * @return  boolean TRUE if the property exists, FALSE otherwise
     */
    public function has($property)
    {
        return isset($this->$property);
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
     * @return 	string 	The title of the view
     */
    public function getTitle()
    {
        return ucfirst($this->getName());
    }

	/**
	 * Get the format
	 *
	 * @return 	string 	The format of the view
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
     * @param  string $contents The contents of the view
     * @return KViewAbstract
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

	/**
	 * Get the model object attached to the controller
	 *
	 * @return	KModelAbstract
	 */
	public function getModel()
	{
		if(!$this->_model instanceof KModelAbstract)
		{
			//Make sure we have a model identifier
		    if(!($this->_model instanceof KObjectIdentifier)) {
		        $this->setModel($this->_model);
			}

		    $this->_model = $this->getObject($this->_model);
		}

		return $this->_model;
	}

	/**
	 * Method to set a model object attached to the view
	 *
	 * @param	mixed	$model An object that implements KObjectInterface, KObjectIdentifier object
	 * 					       or valid identifier string
	 * @return	KViewAbstract
	 */
    public function setModel($model)
	{
		if(!($model instanceof KModelAbstract))
		{
	        if(is_string($model) && strpos($model, '.') === false )
		    {
			    // Model names are always plural
			    if(KStringInflector::isSingular($model)) {
				    $model = KStringInflector::pluralize($model);
			    }

			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('model');
			    $identifier->name	= $model;
			}
			else $identifier = $this->getIdentifier($model);

			$model = $identifier;
		}

		$this->_model = $model;

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

            $translator = $this->getObject($identifier);
        }

        $this->_translator = $translator;

        return $this;
    }

 	/**
     * Get the layout.
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        return $this->_layout;
    }

   /**
     * Sets the layout name to use
     *
     * @param    string  $layout The template name.
     * @return   KViewAbstract
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

	/**
	 * Create a route based on a full or partial query string
	 *
     * index.php? will be automatically added.
	 * option, view and layout can be omitted. The following variations will result in the same route:
	 *
	 * - foo=bar
     * - view=myview&foo=bar
	 * - option=com_mycomp&view=myview&foo=bar
	 *
	 * If the route starts with '&' the query string will be appended to the current URL.
	 *
	 * In templates, use @route()
	 *
     * @param   string|array $route  The query string or array used to create the route
     * @param   boolean      $fqr    If TRUE create a fully qualified route. Defaults to FALSE.
     * @param   boolean      $escape If TRUE escapes the route for xml compliance. Defaults to TRUE.
     * @return  string The route
	 */
	public function createRoute($route = '', $fqr = true, $escape = true)
	{
        if (is_string($route) && substr($route, 0, 1) === '&')
		{
			$url   = clone KRequest::url();
			$vars  = array();
			parse_str(trim($route), $vars);

			$url->setQuery(array_merge($url->getQuery(true), $vars));

			$result = 'index.php?'.$url->getQuery();
		}
		else
		{
            $parts = array();

            if (is_string($route)) {
                parse_str(trim($route), $parts);
            } else {
                $parts = $route;
            }

            if (!isset($parts['option'])) {
                $parts['option'] = 'com_'.$this->getIdentifier()->package;
            }

            if (!isset($parts['view'])) {
                $parts['view'] = $this->getName();
            }

            // Add the layout information to the route only if there is no layout information
            // in the menu item and the current layout is not default
            if (!isset($parts['layout']) && $this->getLayout() !== 'default') {
                $parts['layout'] = $this->getLayout();
            }

            // Add the format information to the URL only if it's not 'html'
            if (!isset($parts['format']) && $this->getIdentifier()->name !== 'html') {
                $parts['format'] = $this->getIdentifier()->name;
            }

            $result = 'index.php?'.http_build_query($parts, '', '&');
		}

		$result = JRoute::_($result, $escape);

        if ($fqr) {
            $result = KRequest::url()->toString(KHttpUrl::AUTHORITY).$result;
        }

        return $result;
	}

	/**
	 * Returns the views output
 	 *
	 * @return 	string
	 */
	public function __toString()
	{
		return $this->display();
	}
}
