<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Koowa System Plugin
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Plugin\System\Koowa
 */
class PlgSystemKoowa extends JPlugin
{
    /**
     * Boots Koowa framework and applies some bug fixes for certain environments
     *
     * @param object $subject
     * @param array  $config
     */
    public function __construct($subject, $config = array())
	{
		// Check if database type is MySQLi
		if(JFactory::getApplication()->getCfg('dbtype') != 'mysqli')
		{
			if (JFactory::getApplication()->getName() === 'administrator') 
			{
				$link   = JRoute::_('index.php?option=com_config');
				$error  = 'In order to use Joomlatools framework, your database type in Global Configuration should be
				           set to <strong>MySQLi</strong>. Please go to <a href="%2$s">Global Configuration</a> and in
				           the \'Server\' tab change your Database Type to <strong>MySQLi</strong>.';

                JFactory::getApplication()->enqueueMessage(sprintf(JText::_($error), $link), 'warning');
			}
			
			return;
		}

        // Try to raise Xdebug nesting level
        @ini_set('xdebug.max_nesting_level', 200);
 		
 		// Set pcre.backtrack_limit to a larger value
 		// See: https://bugs.php.net/bug.php?id=40846
 		if (version_compare(PHP_VERSION, '5.3.6', '<=') && @ini_get('pcre.backtrack_limit') < 1000000) {
 		    @ini_set('pcre.backtrack_limit', 1000000);
 		}

        // 2.5.7+ bug - you always need to supply a toolbar title to avoid notices
        // This happens when the component does not supply an output at all
        if (class_exists('JToolbarHelper')) {
            JToolbarHelper::title('');
        }

        //Bugfix: Set offset according to user's timezone
        if (!JFactory::getUser()->guest)
        {
            if ($offset = JFactory::getUser()->getParam('timezone')) {
                JFactory::getConfig()->set('offset', $offset);
            }
        }

		//Bootstrap the Koowa Framework
        $this->bootstrap();

		parent::__construct($subject, $config);
	}

    /**
     * Adds application response time and memory usage to Chrome Inspector with ChromeLogger extension
     *
     * See: https://chrome.google.com/webstore/detail/chrome-logger/noaneddfkdjfnfdakjjmocngnfkfehhd
     */
    public function __destruct()
    {
        if (JDEBUG && !headers_sent())
        {
            $buffer = JProfiler::getInstance('Application')->getBuffer();
            if ($buffer)
            {
                $data = strip_tags(end($buffer));
                $row = array(array($data), null, 'info');

                $header = array(
                    'version' => '4.1.0',
                    'columns' => array('log', 'backtrace', 'type'),
                    'rows'    => array($row)
                );

                header('X-ChromeLogger-Data: ' . base64_encode(utf8_encode(json_encode($header))));
            }
        }
    }

    /**
     * Bootstrap the Koowa Framework
     *
     * @return bool Returns TRUE if the framework was found and bootstrapped succesfully.
     */
    public function bootstrap()
    {
        // Koowa: setup
        $path = JPATH_LIBRARIES.'/koowa/libraries/koowa.php';
        if (file_exists($path))
        {
            require_once $path;

            $application = JFactory::getApplication()->getName();

            /**
             * Library Bootstrapping
             */
            Koowa::getInstance(array(
                'debug'           => JDEBUG,
                'cache_namespace' => 'koowa-'.$application.'-'.md5(JFactory::getApplication()->getCfg('secret')),
                'cache_enabled'   => false //JFactory::getApplication()->getCfg('caching')
            ));

            $manager     = KObjectManager::getInstance();
            $loader      = $manager->getClassLoader();

            //Application basepaths
            $loader->registerBasepath('site' , JPATH_SITE);
            $loader->registerBasepath('admin', JPATH_ADMINISTRATOR);

            //Component locator
            $loader->registerLocator(new KClassLocatorComponent(array(
                'namespaces' => array(
                    '\\'         => JPATH_BASE,
                    'Koowa'      => JPATH_LIBRARIES.'/koowa',
                )
            )));

            $manager->registerLocator('lib:object.locator.component');

            //Module Locator
            $loader->registerLocator(new ComKoowaClassLocatorModule(array(
                'namespaces' => array(
                    '\\'     => JPATH_BASE,
                    'Koowa'  => JPATH_LIBRARIES.'/koowa',

                )
            )));

            $manager->registerLocator('com:koowa.object.locator.module');

            //Plugin Locator
            $loader->registerLocator(new ComKoowaClassLocatorPlugin(array(
                'namespaces' => array(
                    '\\'     => JPATH_ROOT,
                    'Koowa'  => JPATH_LIBRARIES.'/koowa',
                )
            )));

            $manager->registerLocator('com:koowa.object.locator.plugin');

            /**
             * Component Bootstrapping
             */
            $manager->getObject('com:koowa.bootstrapper')->bootstrap($application);

            //Setup the request
            $request = $manager->getObject('request');

            // Get the URL from Joomla if live_site is set
            if (JFactory::getApplication()->getCfg('live_site'))
            {
                $request->setBasePath(rtrim(JURI::base(true), '/\\'));
                $request->setBaseUrl($manager->getObject('lib:http.url', array('url' => JURI::base())));
            }

            //Exception Handling
            if (PHP_SAPI !== 'cli') {
                $manager->getObject('event.publisher')->addListener('onException', array($this, 'onException'), KEvent::PRIORITY_LOW);
            }

            /**
             * Plugin Bootstrapping
             */
            JPluginHelper::importPlugin('koowa', null, true);

            return true;
        }

        return false;
    }

    /*
     * Joomla Compatibility
     *
     * For Joomla 3.x : Re-run the routing and add returned keys to the $_GET request. This is done because Joomla 3
     * sets the results of the router in $_REQUEST and not in $_GET
     */
    public function onAfterRoute()
    {
        if (class_exists('Koowa'))
        {
            $request = KObjectManager::getInstance()->getObject('request');

            $app = JFactory::getApplication();
            if ($app->isSite())
            {
                $uri     = clone JURI::getInstance();

                $router = JFactory::getApplication()->getRouter();
                $result = $router->parse($uri);

                foreach ($result as $key => $value)
                {
                    if (!$request->query->has($key)) {
                        $request->query->set($key, $value);
                    }
                }
            }

            if ($request->query->has('limitstart')) {
                $request->query->offset = $request->query->limitstart;
            }
        }
    }

    /*
     * Joomla Compatibility
     *
     * For Joomla 2.5 and 3.x : Handle session messages if they have not been handled by Koowa for example after a
     * redirect to a none Koowa component.
     */
    public function onAfterDispatch()
    {
        if (class_exists('Koowa'))
        {
            $messages = KObjectManager::getInstance()->getObject('user')->getSession()->getContainer('message')->all();

            foreach($messages as $type => $group)
            {
                if ($type === 'success') {
                    $type = 'message';
                }

                foreach($group as $message) {
                    JFactory::getApplication()->enqueueMessage($message, $type);
                }
            }
        }
    }

    /**
     * Exception event handler
     *
     * @param KEventException $event
     */
    public function onException(KEventException $event)
    {
        KObjectManager::getInstance()->getObject('com:koowa.dispatcher.http')->fail($event);
        return true;
    }
}
