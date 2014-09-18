<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
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
     * View object or identifier (com://APP/COMPONENT.view.NAME.FORMAT)
     *
     * @var string|object
     */
    protected $_view;

    /**
     * List of formats supported by the controller
     *
     * @var array
     */
    protected $_formats;

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

        //Set the supported formats
        $this->_formats = KObjectConfig::unbox($config->formats);

        // Mixin the toolbar interface
        $this->mixin('lib:controller.toolbar.mixin');

        //Attach the toolbars
        $this->addCommandCallback('before.render', '_addToolbars', array('toolbars' => $config->toolbars));

        //Load the controller translations
        $this->addCommandCallback('before.render', '_loadTranslations');
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
            'formats' => array('html'),
            'view'    => $this->getIdentifier()->name,
            'toolbars'  => array()
         ));

        parent::_initialize($config);
    }

    /**
     * Add the toolbars to the controller
     *
     * @param KControllerContextInterface $context
     */
    protected function _addToolbars(KControllerContextInterface $context)
    {
        if($this->getView() instanceof KViewHtml)
        {
            // Add toolbars on authenticated requests only.
            if($this->getObject('user')->isAuthentic())
            {
                foreach($context->toolbars as $toolbar) {
                    $this->addToolbar($toolbar);
                }
            }

            $this->getView()->getTemplate()->addFilter('toolbar', array('toolbars' => $this->getToolbars()));
        }
    }

    /**
     * Load the controller translations
     *
     * @param KControllerContextInterface $context
     * @return void
     */
    protected function _loadTranslations(KControllerContextInterface $context)
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
     * Get the view object attached to the controller
     *
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
                'url'        => $this->getObject('request')->getUrl(),
                'layout'     => $this->getRequest()->getQuery()->get('layout', 'identifier'),
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
        }

        return $this->_view;
    }

    /**
     * Method to set a view object attached to the controller
     *
     * @param   mixed   $view An object that implements KObjectInterface, KObjectIdentifier object
     *                  or valid identifier string
     * @return  object  A KViewInterface object or a KObjectIdentifier object
     */
    public function setView($view)
    {
        if(!($view instanceof KViewInterface))
        {
            if(is_string($view) && strpos($view, '.') === false )
            {
                $identifier			= $this->getIdentifier()->toArray();
                $identifier['path']	= array('view', $view);
                $identifier['name']	= $this->getRequest()->getFormat();

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($view);

            $view = $identifier;
        }

        $this->_view = $view;

        return $this->_view;
    }

    /**
     * Get the supported formats
     *
     * Method dynamically adds the 'json' format if the user is authentic.
     *
     * @return array
     */
    public function getFormats()
    {
        $result = $this->_formats;
        if($this->getUser()->isAuthentic()) {
            $result[] = 'json';
        }

        return $result;
    }

    /**
     * Render action
     *
     * This function will check if the format is supported and if not throw a 406 Not Accepted exception. It will also
     * set the rendered output in the response after it has been created.
     *
     * @param  KControllerContextInterface $context A command context object
     * @throws KControllerExceptionFormatNotSupported If the requested format is not supported for the resource
     * @return string|bool The rendered output of the view or false if something went wrong
     */
    protected function _actionRender(KControllerContextInterface $context)
    {
        $format = $this->getRequest()->getFormat();

        //Check if the format is supported
        if(in_array($format, $this->getFormats()))
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
        }
        else throw new KControllerExceptionFormatNotSupported('Format: '.$format.' not supported');

        return $content;
    }

    /**
     * Supports a simple form Fluent Interfaces. Allows you to set the request properties by using the request property
     * name as the method name.
     *
     * For example : $controller->view('name')->layout('name')->format('html')->render();
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
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

                if($method == 'format') {
                    $this->getRequest()->setFormat($args[0]);
                }

                if($method == 'layout') {
                    $this->getRequest()->getQuery()->set($method, $args[0]);
                }

                return $this;
            }
        }

        return parent::__call($method, $args);
    }
}