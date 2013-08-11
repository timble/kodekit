<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Default Dispatcher
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaDispatcher extends KDispatcherDefault implements KServiceInstantiatable
{
 	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        /*
         * Re-run the routing and add returned keys to the $_GET request
         * This is done because Joomla 3 sets the results of the router in $_REQUEST and not in $_GET
         */
        $app = JFactory::getApplication();
        if ($app->isSite() && $app->getCfg('sef'))
        {
            $uri = clone JURI::getInstance();

            $router = JFactory::getApplication()->getRouter();
            $result = $router->parse($uri);

            foreach ($result as $key => $value)
            {
                if (!KRequest::has('get.'.$key)) {
                    KRequest::set('get.'.$key, $value);
                }
            }
        }

        parent::_initialize($config);

        //Force the controller to the information found in the request
        if($config->request->view) {
            $config->controller = $config->request->view;
        }
    }

	/**
     * Force creation of a singleton
     *
     * @param   KConfig $config Configuration options
     * @param 	object	A KServiceInterface object
     * @return KDispatcherDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
       // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);

            //Add the factory map to allow easy access to the singleton
            $container->setAlias('dispatcher', $config->service_identifier);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Dispatch the controller and redirect
     *
     * This function divert the standard behavior and will redirect if no view
     * information can be found in the request.
     *
     * @param   string      The view to dispatch. If null, it will default to
     *                      retrieve the controller information from the request or
     *                      default to the component name if no controller info can
     *                      be found.
     *
     * @return  KDispatcherDefault
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        //Redirect if no view information can be found in the request
        if(!KRequest::has('get.view'))
        {
            $url = clone(KRequest::url());
            $url->query['view'] = $this->getController()->getView()->getName();

            JFactory::getApplication()->redirect($url);
        }

        return parent::_actionDispatch($context);
    }

    /**
     * Push the controller data into the document
     *
     * This function divert the standard behavior and will push specific controller data
     * into the document
     *
     * @return  KDispatcherDefault
     */
    protected function _actionRender(KCommandContext $context)
    {
        $view  = $this->getController()->getView();

        JFactory::getDocument()->setMimeEncoding($view->mimetype);

        //Disabled the application menubar
        if(JFactory::getApplication()->isAdmin() && $this->getController()->isEditable() && KInflector::isSingular($view->getName())) {
            KRequest::set('get.hidemainmenu', 1);
        }

        return parent::_actionRender($context);
    }
}
