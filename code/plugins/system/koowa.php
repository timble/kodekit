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
	public function __construct($subject, $config = array())
	{
		// Turn off E_STRICT errors for now
		error_reporting(error_reporting() & ~E_STRICT);
		
	    // Command line fixes for Joomla
		if (PHP_SAPI === 'cli')
		{
			if (!isset($_SERVER['HTTP_HOST'])) {
				$_SERVER['HTTP_HOST'] = '';
			}

			if (!isset($_SERVER['REQUEST_METHOD'])) {
				$_SERVER['REQUEST_METHOD'] = '';
			}
		}

	    // Check if Koowa is active
		if(JFactory::getApplication()->getCfg('dbtype') != 'mysqli')
		{
    		JError::raiseWarning(0, JText::_("Koowa plugin requires MySQLi Database Driver. Please change your database configuration settings to 'mysqli'"));
    		return;
		}

	    //Safety Extender compatibility
		if(extension_loaded('safeex') && strpos('tmpl', @ini_get('safeex.url_include_proto_whitelist')) === false)
		{
		    $whitelist = @ini_get('safeex.url_include_proto_whitelist');
		    $whitelist = (strlen($whitelist) ? $whitelist . ',' : '') . 'tmpl';
		    @ini_set('safeex.url_include_proto_whitelist', $whitelist);
 		}
 		
 		// Set pcre.backtrack_limit to a larger value
 		// See: https://bugs.php.net/bug.php?id=40846
 		if (version_compare(PHP_VERSION, '5.3.6', '<=') && @ini_get('pcre.backtrack_limit') < 1000000) {
 		    @ini_set('pcre.backtrack_limit', 1000000);
 		}

		//Set constants
		define('KDEBUG'      , JDEBUG);

		//Set path definitions
		define('JPATH_FILES' , JPATH_ROOT);
		define('JPATH_IMAGES', JPATH_ROOT.DS.'images');

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
		JPluginHelper::importPlugin('koowa', null, true, KService::get('com://admin/default.event.dispatcher'));

	    //Bugfix : Set offset accoording to user's timezone
		if(!JFactory::getUser()->guest)
		{
		   if($offset = JFactory::getUser()->getParam('timezone')) {
		        JFactory::getConfig()->setValue('config.offset', $offset);
		   }
		}
		
		// Load language files for the framework
		KService::get('com:default.translator')->loadLanguageFiles();

		parent::__construct($subject, $config);
	}

	/**
	 * On after intitialse event handler
	 *
	 * This functions implements HTTP Basic authentication support
	 *
	 * @return void
	 */
	public function onAfterInitialise()
	{
	    /*
	     * Try to log the user in
	     *
	     * If the request contains authorization information we try to log the user in
	     */
	    if($this->params->get('auth_basic', 0) && JFactory::getUser()->guest) {
	        $this->_authenticateUser();
	    }

	    /*
	     * Reset the user and token
	     *
	     * In case another plugin have logged in after we initialized we need to reset the token and user object
	     * One plugin that could cause that, are the Remember Me plugin
	     */
	     if(!JFactory::getUser()->guest) {
	         KRequest::set('request._token', JUtility::getToken());
	     }

	     /*
	      * TODO: use a --koowa flag here
	     * Dispatch the default dispatcher
	     *
	     * If we are running in CLI mode bypass the default Joomla executition chain and dispatch the default
	     * dispatcher.
	     */
	    if (PHP_SAPI === 'cli')
	    {
	    	$url = null;
	    	foreach ($_SERVER['argv'] as $arg)
	    	{
	    		if (strpos($arg, '--url') === 0)
	    		{
	    			$url = str_replace('--url=', '', $arg);
	    			if (strpos($url, '?') === false) {
	    				$url = '?'.$url;
	    			}
	    			break;
	    		}
	    	}

	    	if (!empty($url))
	    	{
	    		$component = 'default';
	    		$url = KService::get('koowa:http.url', array('url' => $url));
    			if (!empty($url->query['option'])) {
    				$component = substr($url->query['option'], 4);
    			}

	    		// Thanks Joomla. We will take it from here.
	    		echo KService::get('com:'.$component.'.dispatcher.cli')->dispatch();
	    		exit(0);
	    	}
	    }
	}
	
	/**
	 * Reset the JDocument object to the proper format if it comes from the HTTP accept header
	 */
	public function onAfterRoute()
	{
    	if(!KRequest::has('request.format'))
    	{
    		$format = KRequest::format();

    		// Reset the document per the Koowa format
    		if ($format) {
    			$this->_resetDocument($format);
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

		$this->errorHandler($exception);
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

	    if(JFactory::getConfig()->getValue('config.debug')) {
			$error->set('message', (string) $this->_exception);
		} else {
			$error->set('message', KHttpResponse::getMessage($error->get('code')));
		}

	    if($this->_exception->getCode() == KHttpResponse::UNAUTHORIZED) {
		   header('WWW-Authenticate: Basic Realm="'.KRequest::base().'"');
		}

		//Make sure the buffers are cleared
		while(@ob_get_clean());

		JError::customErrorPage($error);
	}

	/**
	 * Basic authentication support
	 *
	 * This functions tries to log the user in if authentication credentials are
	 * present in the request.
	 *
	 * @return boolean	Returns TRUE is basic authentication was successful
	 */
	protected function _authenticateUser()
	{
	    if(KRequest::has('server.PHP_AUTH_USER') && KRequest::has('server.PHP_AUTH_PW'))
	    {
	        $credentials = array(
	            'username' => KRequest::get('server.PHP_AUTH_USER', 'url'),
	            'password' => KRequest::get('server.PHP_AUTH_PW'  , 'url'),
	        );

	        if(JFactory::getApplication()->login($credentials) !== true)
	        {
	            throw new KException('Login failed', KHttpResponse::UNAUTHORIZED);
        	    return false;
	        }

	        //Force the token
	        KRequest::set('request._token', JUtility::getToken());

	        return true;
	    }

	    return false;
	}

	protected function _resetDocument($format)
	{
		$format_joomla = JRequest::getWord('format');
	
		JRequest::setVar('format', $format);

		JFactory::$document = null;
		JFactory::getDocument();
	
		if ($format_joomla) {
			JRequest::setVar('format', $format_joomla);
		}
	}
}