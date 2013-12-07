<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract View Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerView extends KControllerAbstract implements KControllerViewable
{
	/**
	 * URL for redirection.
	 *
	 * @var	string
	 */
	protected $_redirect = null;

	/**
	 * Redirect message.
	 *
	 * @var	string
	 */
	protected $_redirect_message = null;

	/**
	 * Redirect message type.
	 *
	 * @var	string
	 */
	protected $_redirect_type = 'message';

	/**
	 * View object or identifier (com://APP/COMPONENT.view.NAME.FORMAT)
	 *
	 * @var	string|object
	 */
	protected $_view;

	/**
	 * Constructor
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		// Set the view identifier
		$this->_view = $config->view;
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'view' => $this->getIdentifier()->name,
         ));

        parent::_initialize($config);
    }

    /**
     * Get the view object attached to the controller
     *
     * If we are dispatching this controller this function will check if the view folder exists. If not it will throw
     * an exception. This is a security measure to make sure we can only explicitly get data from views the have been
     * physically defined.
     *
     * @throws  KControllerExceptionNotFound If the view cannot be found. Only when controller is being dispatched.
     * @throws	UnexpectedValueException	If the views doesn't implement the KViewInterface
     * @return	KViewInterface
     */
    public function getView()
    {
        if(!$this->_view instanceof KViewInterface)
        {
            //Make sure we have a view identifier
            if(!($this->_view instanceof KObjectIdentifier)) {
                $this->setView($this->_view);
            }

            //Create the view
            $config = array(
                'url'	     => $this->getObject('request')->getUrl(),
                'layout'     => $this->getRequest()->query->get('layout', 'identifier'),
                'auto_fetch' => $this instanceof KControllerModellable
            );

            $this->_view = $this->getObject($this->_view, $config);

            //Make sure the view implements KViewInterface
            if(!$this->_view instanceof KViewInterface)
            {
                throw new UnexpectedValueException(
                    'View: '.get_class($this->_view).' does not implement KViewInterface'
                );
            }

            //Make sure the view exists if we are dispatching this controller
            if($this->isDispatched())
            {
                $identifier = $this->_view->getIdentifier();
                $classname = 'Com' . ucfirst($identifier->package) . KStringInflector::camelize(implode('_', $identifier->path)) . ucfirst($identifier->name);
                $path       = $this->getObject('manager')->getClassLoader()->findPath($classname, $identifier->basepath);

                if(!file_exists(dirname($path))) {
                    throw new KControllerExceptionNotFound('View: '.$this->_view->getName().' not found');
                }
            }
		}

		return $this->_view;
	}

	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param	mixed	$view An object that implements KObjectInterface, KObjectIdentifier object
	 * 					or valid identifier string
	 * @return	object	A KViewInterface object or a KObjectIdentifier object
	 */
	public function setView($view)
	{
		if(!($view instanceof KViewInterface))
		{
			if(is_string($view) && strpos($view, '.') === false )
		    {
			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('view', $view);
			    $identifier->name	= $this->getRequest()->getFormat();
			}
			else $identifier = $this->getIdentifier($view);

			$view = $identifier;
		}

		$this->_view = $view;

		return $this->_view;
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @param	string  $url URL to redirect to.
	 * @param	string	$msg Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param	string	$type Message type. Optional, defaults to 'message'.
	 * @return	KControllerAbstract
	 */
	public function setRedirect( $url, $msg = null, $type = 'message' )
	{
		$this->_redirect   		 = $url;
		$this->_redirect_message = $msg;
		$this->_redirect_type	 = $type;

		return $this;
	}

	/**
	 * Returns an array with the redirect url, the message and the message type
	 *
	 * @return array Named array containing url, message and messageType, or null if no redirect was set
	 */
	public function getRedirect()
	{
		$result = array();

		if(!empty($this->_redirect))
		{
			$result = array(
				'url' 		=> JRoute::_($this->_redirect, false),
				'message' 	=> $this->_redirect_message,
				'type' 		=> $this->_redirect_type,
			);
		}

		return $result;
	}

    /**
     * Render action
     *
     * This function will also set the rendered output in the response.
     *
     * @param KControllerContextInterface $context A command context object
     * @return    string|bool    The rendered output of the view or false if something went wrong
     */
	protected function _actionRender(KControllerContextInterface $context)
	{
        $view = $this->getView();

        //Push the content in the view, used for view decoration
        $view->setContent($context->response->getContent());

        //Render the view
        $param = KObjectConfig::unbox($context->param);

        if(is_array($param)) {
            $data = (array) $param;
        } else {
            $data = array();
        }

        $content = $view->render($data);

        //Set the data in the response
        $context->response->setContent($content, $view->mimetype);

        return $content;
	}

    /**
     * Supports a simple form Fluent Interfaces. Allows you to set the request properties by using the request property
     * name as the method name.
     *
     * For example : $controller->view('name')->layout('name')->format('html')->render();
     *
     * @param	string	$method Method name
     * @param	array	$args   Array containing all the arguments for the original call
     * @return	KControllerView
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        if(!isset($this->_mixed_methods[$method]))
        {
            if(in_array($method, array('layout', 'view', 'format')))
            {
                if($method == 'view') {
                    $this->setView($args[0]);
                }

                $this->getRequest()->query->set($method, $args[0]);
                return $this;
            }
        }

        return parent::__call($method, $args);
    }
}
