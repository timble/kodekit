<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package 	Koowa_Router
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Router Proxy
 *
 * @author 		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package 	Koowa_Router
 * @uses 		KPatternCommandChain
 * @uses 		KPatternProxy
 */
class KRouter extends KPatternProxy
{
	/**
	 * The commandchain
	 *
	 * @var	object
	 */
	protected $_commandChain = null;

	/**
	 * Constructor
	 *
	 * @param	object	$router	The router object to proxy
	 */
	public function __construct($router)
	{
		parent::__construct($router);
		
		 //Create the command chain
        $this->_commandChain = new KPatternCommandChain();
        $this->_commandChain->enqueue(new KCommandEvent());

		//Attach the router rules
		$this->getObject()->attachBuildRule(array($this, 'onBuildRoute'));
		$this->getObject()->attachParseRule(array($this, 'onParseRoute'));
	}
	
	/**
	 * Proxy the router parse function to fix a bug in the core
	 *
	 * @param	object	$url	The URI to parse
	
	 * @return	array
	 */
	public function parse(&$uri)
	{
		$vars = array();
		
		// Fool the parent in thinking we are coming in through index
		$path = $uri->getPath();
		$path = str_replace('index2.php', 'index.php', $path);
		$uri->setPath($path);

		$vars += $this->getObject()->parse($uri);
		
		return $vars;
	}

	/**
	 * Build route callback function
	 *
	 * @see JRouter::_processBuildRules()
	 */
	public function onBuildRoute($router, $uri)
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['notifier']     = $this;
		$args['uri']    	  = $uri;
	
		$this->_commandChain->run('onRouterBuildRoute', $args);
	}

	/**
	 * Parse route callback function
	 *
	 * @return	array
	 * @see JRouter::_processParseRules()
	 */
	public function onParseRoute($router, $uri)
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['notifier']     = $this;
		$args['uri']    	  = $uri;
		$args['vars']		  = array();
	
		$this->_commandChain->run('onRouterParseRoute', $args);
		return $args['vars'];
	}
}