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
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        //Set the view url
        $this->setUrl($config->url);

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
            'layout'     => '',
            'url'        =>  $this->getObject('koowa:http.url')
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
     * @param   mixed  $property The property name.
     * @param   mixed   $value    The property value.
     * @return KViewAbstract
     */
    public function set($property, $value = null)
    {
        if (is_array($property)) {
            foreach($property as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            $this->$property = $value;
        }

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
    public function get($property = null, $default = null)
    {
        if (is_null($property)) {
            throw new InvalidArgumentException('Invalid property name in'.get_class($this).'::get');
        }

        return isset($this->$property) ? $this->$property : $default;
    }

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
     * @param  string $content The contents of the view
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
		if(!$this->_model instanceof KModelInterface)
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
		if(!($model instanceof KModelInterface))
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
     * - option=com_mycomp&view=myview&foo=bar
	 *
	 * In templates, use @route()
	 *
     * @param   string|array $route  The query string or array used to create the route
     * @param   boolean      $fqr    If TRUE create a fully qualified route. Defaults to FALSE.
     * @param   boolean      $escape If TRUE escapes the route for xml compliance. Defaults to TRUE.
     * @return  KHttpUrl     The route
	 */
	public function getRoute($route = '', $fqr = true, $escape = true)
	{
        //Parse route
        $parts = array();

        //@TODO : Check if $route if valid. Throw exception if not.
        if(is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        //Check to see if there is component information in the route if not add it
        if (!isset($parts['option'])) {
            $parts['option'] = 'com_' . $this->getIdentifier()->package;
        }

        //Add the view information to the route if it's not set
        if (!isset($parts['view'])) {
            $parts['view'] = $this->getName();
        }

        //Add the format information to the route only if it's not 'html'
        if (!isset($parts['format'])) {
            $parts['format'] = $this->getIdentifier()->name;
        }

        //Add the model state only for routes to the same view
        if ($parts['option'] == 'com_'.$this->getIdentifier()->package && $parts['view'] == $this->getName())
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

        $url = JRoute::_('index.php?'.http_build_query($parts), $escape);

        //Add the host and the schema
        if ($fqr === true) {
            $url = $this->getUrl()->toString(KHttpUrl::AUTHORITY) . '/' . $url;
        }

        $route = $this->getObject('koowa:http.url', array('url' => $url));

        return $route;
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
