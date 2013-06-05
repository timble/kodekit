<?php
/**
 * @version     $Id: koowa.php 4478 2012-02-10 01:50:39Z johanjanssens
 * @package     Nooku_Plugins
 * @subpackage  System
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Koowa System plugin
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Plugins
 * @subpackage  System
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemKoowa extends JPlugin
{
    protected $_request_before_route = array();

	public function __construct($subject, $config = array())
	{
        // Turn off E_STRICT errors for now
        error_reporting(error_reporting() & ~E_STRICT);

        // Check if database type is MySQLi
		if(JFactory::getApplication()->getCfg('dbtype') != 'mysqli')
		{
			if (JFactory::getApplication()->getName() === 'administrator') 
			{
				$string = version_compare(JVERSION, '1.6', '<') ? 'mysqli' : 'MySQLi';
				$link   = JRoute::_('index.php?option=com_config');
				$error  = 'In order to use Joomlatools framework, your database type in Global Configuration should be set to <strong>%1$s</strong>. Please go to <a href="%2$s">Global Configuration</a> and in the \'Server\' tab change your Database Type to <strong>%1$s</strong>.';
				JError::raiseWarning(0, sprintf(JText::_($error), $string, $link));
			}
			
			return;
		}
 		
 		// Set pcre.backtrack_limit to a larger value
 		// See: https://bugs.php.net/bug.php?id=40846
 		if (version_compare(PHP_VERSION, '5.3.6', '<=') && @ini_get('pcre.backtrack_limit') < 1000000) {
 		    @ini_set('pcre.backtrack_limit', 1000000);
 		}

		//Set constants
		define('KDEBUG', JDEBUG);

        //Set path definitions
        define('JPATH_FILES' , JPATH_ROOT);
        define('JPATH_IMAGES', JPATH_ROOT.DIRECTORY_SEPARATOR.'images');

        //Set exception handler
		set_exception_handler(array($this, 'exceptionHandler'));

		// Koowa : setup
        require_once( JPATH_LIBRARIES.'/koowa/koowa.php');
        Koowa::getInstance(array(
			'cache_prefix'  => md5(JFactory::getApplication()->getCfg('secret')).'-cache-koowa',
			'cache_enabled' => false //JFactory::getApplication()->getCfg('caching')
        ));

        KLoader::addAdapter(new KLoaderAdapterModule(array('basepath' => JPATH_BASE)));
        KLoader::addAdapter(new KLoaderAdapterPlugin(array('basepath' => JPATH_ROOT)));
        KLoader::addAdapter(new KLoaderAdapterComponent(array('basepath' => JPATH_BASE)));

        KServiceIdentifier::addLocator(KService::get('koowa:service.locator.module'));
        KServiceIdentifier::addLocator(KService::get('koowa:service.locator.plugin'));
        KServiceIdentifier::addLocator(KService::get('koowa:service.locator.component'));

        KServiceIdentifier::setApplication('site' , JPATH_SITE);
        KServiceIdentifier::setApplication('admin', JPATH_ADMINISTRATOR);

        KService::setAlias('koowa:database.adapter.mysqli', 'com://admin/default.database.adapter.mysqli');
		KService::setAlias('translator', 'com:default.translator');

	    //Setup the request
	    if (JFactory::getApplication()->getName() !== 'site') {
	    	KRequest::root(str_replace('/'.JFactory::getApplication()->getName(), '', KRequest::base()));
	    }

		//Load the koowa plugins
		JPluginHelper::importPlugin('koowa', null, true);

	    //Bugfix : Set offset accoording to user's timezone
		if(!JFactory::getUser()->guest)
		{
		   if($offset = JFactory::getUser()->getParam('timezone'))
           {
               if (version_compare(JVERSION, '3.0', '>=')) {
                   JFactory::getConfig()->set('offset', $offset);
               } else {
                   JFactory::getConfig()->setValue('config.offset', $offset);
               }
		   }
		}
		
		// Load language files for the framework
		KService::get('com:default.translator')->loadLanguageFiles();

		parent::__construct($subject, $config);
	}

    /**
     * Save the initial request keys before routing to set them in $_GET afterwards
     */
    public function onAfterInitialise()
    {
        if (version_compare(JVERSION, '3.0', '>=') && JFactory::getApplication()->isSite()) {
            $this->_request_before_route = array_keys(JFactory::getApplication()->input->getArray($_REQUEST));
        }
    }

    /**
     * Find the new request keys and set them in $_GET
     *
     * This is done because Joomla 3 sets the results of the router in $_REQUEST and not in $_GET
     */
    public function onAfterRoute()
    {
        if (version_compare(JVERSION, '3.0', '>=') && JFactory::getApplication()->isSite())
        {
            $input = JFactory::getApplication()->input;
            $request_after_route = array_keys($input->getArray($_REQUEST));

            $new_keys = array_diff($request_after_route, $this->_request_before_route);
            $values   = $input->getArray(array_fill_keys($new_keys, ''));

            foreach ($values as $key => $value) {
                KRequest::set('get.'.$key, $value);
            }
        }
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
	 * Catch all exception handler
	 *
	 * Calls the Joomla error handler to process the exception
	 *
	 * @param object an Exception object
	 * @return void
	 */
	public function exceptionHandler($exception)
	{
		$this->_exception = $exception; //store the exception for later use

		if ($exception instanceof KException || $exception instanceof JException) {
            $this->errorHandler($exception);
        }
		//Change the Joomla error handler to our own local handler and call it
		JError::setErrorHandling( E_ERROR, 'callback', array($this,'errorHandler'));

		//Make sure we have a valid status code
		JError::raiseError(KHttpResponse::isError($exception->getCode()) ? $exception->getCode() : 500, $exception->getMessage());
	}

	/**
	 * Custom JError callback
	 *
	 * Push the exception call stack in the JException returned through the call back
	 * adn then rener the custom error page
	 *
	 * @param object A JException object
	 * @return void
	 */
	public function errorHandler($error)
	{
		$error->setProperties(array(
			'backtrace'	=> $this->_exception->getTrace(),
			'file'		=> $this->_exception->getFile(),
			'line'		=> $this->_exception->getLine()
		));

		$debug = version_compare(JVERSION, '3.0', 'ge')
			? JFactory::getConfig()->get('debug')
			: JFactory::getConfig()->getValue('config.debug');

	    if($debug) {
			$error->set('message', (string) $this->_exception);
		} else {
			$error->set('message', KHttpResponse::getMessage($error->get('code')));
		}

		//Make sure the buffers are cleared
		while(@ob_get_clean());

		JError::customErrorPage($error);
	}
}