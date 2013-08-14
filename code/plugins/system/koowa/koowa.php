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
				$error  = 'In order to use Joomlatools framework, your database type in Global Configuration should be set to <strong>MySQLi</strong>. Please go to <a href="%2$s">Global Configuration</a> and in the \'Server\' tab change your Database Type to <strong>MySQLi</strong>.';
				JError::raiseWarning(0, sprintf(JText::_($error), $link));
			}
			
			return;
		}
 		
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

		//Set constants
		define('KDEBUG', JDEBUG);

        //Set exception handler
        if (JDEBUG) {
            set_exception_handler(array($this, 'exceptionHandler'));
        }

		// Koowa: setup
        $path = JPATH_LIBRARIES.'/koowa/libraries/koowa.php';
        if (file_exists($path))
        {
            require_once $path;

            Koowa::getInstance(array(
                'cache_prefix'  => md5(JFactory::getApplication()->getCfg('secret')).'-cache-koowa',
                'cache_enabled' => false //JFactory::getApplication()->getCfg('caching')
            ));

            $loader = KService::get('koowa:class.loader');

            $loader->registerLocator(new KClassLocatorModule(array(
                'basepaths' => array('*' => JPATH_BASE, 'koowa' => JPATH_LIBRARIES.'/koowa')
            )));

            $loader->registerLocator(new KClassLocatorPlugin(array(
                'basepaths' => array('*' => JPATH_ROOT, 'koowa' => JPATH_LIBRARIES.'/koowa')
            )));

            $loader->registerLocator(new KClassLocatorComponent(array(
                'basepaths' => array(
                    '*'          => JPATH_BASE,
                    'koowa'      => JPATH_LIBRARIES.'/koowa',
                    'files'      => JPATH_LIBRARIES.'/koowa',
                    'activities' => JPATH_LIBRARIES.'/koowa'
                )
            )));

            KServiceIdentifier::addLocator(KService::get('koowa:service.locator.module'));
            KServiceIdentifier::addLocator(KService::get('koowa:service.locator.plugin'));
            KServiceIdentifier::addLocator(KService::get('koowa:service.locator.component'));

            KServiceIdentifier::registerApplication('site' , JPATH_SITE);
            KServiceIdentifier::registerApplication('admin', JPATH_ADMINISTRATOR);

            KServiceIdentifier::registerPackage('koowa'     , JPATH_LIBRARIES.'/koowa');
            KServiceIdentifier::registerPackage('files'     , JPATH_LIBRARIES.'/koowa');
            KServiceIdentifier::registerPackage('activities', JPATH_LIBRARIES.'/koowa');

            KService::setAlias('koowa:database.adapter.mysqli', 'com://admin/koowa.database.adapter.mysqli');
            KService::setAlias('translator', 'com:koowa.translator');

            //Setup the request
            if (JFactory::getApplication()->getName() !== 'site') {
                KRequest::root(str_replace('/'.JFactory::getApplication()->getName(), '', KRequest::base()));
            }

            //Load the koowa plugins
            JPluginHelper::importPlugin('koowa', null, true);

            //Bugfix: Set offset according to user's timezone
            if (!JFactory::getUser()->guest)
            {
                if ($offset = JFactory::getUser()->getParam('timezone')) {
                    JFactory::getConfig()->set('offset', $offset);
                }
            }
        }

		parent::__construct($subject, $config);
	}
	
	/**
	 * Set the disposition to inline for JSON requests
	 */
	public function onAfterRender()
	{
		if (JFactory::getDocument()->getType() !== 'json') {
			return;
		}
		
		$headers = JResponse::getHeaders();
		foreach ($headers as $key => $header)
		{
			if ($header['name'] === 'Content-disposition')
			{
				$string = $header['value'];
				if (strpos($string, 'attachment; ') !== false) 
				{
					$string = str_replace($string, 'attachment; ', 'inline; ');
					JResponse::setHeader('Content-disposition', $string, true);
					break;
				}
			}
		}
	}

 	/**
	 * Custom exception handler
	 *
	 * @param Exception $exception an Exception object
	 * @return void
	 */
	public function exceptionHandler(Exception $exception)
	{
        try
        {
            // If Koowa does not exist let Joomla handle the exception
            if (!class_exists('Koowa') || !class_exists('ComKoowaTemplateDefault')) {
                throw new Exception('');
            }

            $data = array(
                'exception' => $exception
            );

            $template = KService::get('com:koowa.template.default');
            $template->addFilter(array('alias', 'shorttag', 'variable'));
            $template->loadFile(
                JPATH_ROOT.'/libraries/koowa/components/com_koowa/views/debug/tmpl/error.php',
                $data
            );

            while (@ob_end_clean());

            if (!headers_sent()) {
                header('Content-Type: text/html');
            }

            echo $template->render();

            exit;
        }
        catch (Exception $e)
        {
            if (version_compare(JVERSION, '3.0', '>=')) {
                JErrorPage::render($exception);
            } else {
                JError::raiseError($exception->getCode(), $exception->getMessage());
            }
        }
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
                    'rows' => array($row)
                );

                header('X-ChromeLogger-Data: ' . base64_encode(utf8_encode(json_encode($header))));
            }
        }
    }
}
