<?php
/**
* @version $Id$
* @package Koowa_Application
* @copyright Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link http://www.koowa.org
*/
 
/**
* Application Class
*
* @author Johan Janssens <johan@joomlatools.org>
* @package Koowa_Application
* @uses KPatternCommandChain
* @uses KPatternProxy
*/
class KApplication extends KPatternProxy
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
	 * @param	object	$dbo 	The application object to proxy
	 * @return	void
	 */
	public function __construct($app)
	{
		parent::__construct($app);
		
		 //Create the command chain
        $this->_commandChain = new KPatternCommandChain();
	}
	
	/**
	 * Proxy the application getName() method
	 */
	function getName()
	{
		//Create a shortcut for the administrator name
		$name = $this->_object->getName();
		if($name == 'administrator') {
			$name = 'admin';
		}
		
		return $name;
	}
	
  	/**
	 * Proxy the application initialise() method
	 *
	 * @param	array An optional associative array of configuration settings
	 * @return	mixed|false The value returned by the proxied method, false in error case.
	 */
	public function initialise(array $options = array())
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name'] = get_class($this);
		$args['options']    = $options;
	
		if($this->_commandChain->run('onBeforeApplicationInitialise', $args) === true) {
			$args['result'] = $this->getObject()->initialise($args['options']);
			$this->_commandChain->run('onAfterApplicationInitialise', $args);
		}

		return $args['result'];
	}
	
  	/**
	 * Proxy the application route() method
	 * 
	 * @return	mixed|false The value returned by the proxied method, false in error case.
	 */
	public function route()
 	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name'] = get_class($this);
	
		if($this->_commandChain->run('onBeforeApplicationRoute', $args) === true) {
			$args['result'] = $this->getObject()->route();
			$this->_commandChain->run('onAfterApplicationRoute', $args);
		}

		return $args['result'];
 	}
 	
   	/**
	 * Proxy the application dispatch() method
	 * 
	 * @return	mixed|false The value returned by the proxied method, false in error case.
	 */
 	public function dispatch($component)
 	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name'] = get_class($this);
		$args['component']  = $component;
		
		if($this->_commandChain->run('onBeforeApplicationDispatch', $args) === true) {
			$args['result'] = $this->getObject()->dispatch($args['component']);
			$this->_commandChain->run('onAfterApplicationDispatch', $args);
		}

		return $args['result'];
 	}
 	
	/**
	 * Proxy the application render() method
	 * 
	 * @return	mixed|false The value returned by the proxied method, false in error case.
	 */
	public function render()
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name'] = get_class($this);
		
		if($this->_commandChain->run('onBeforeApplicationRender', $args) === true) {
			$args['result'] = $this->getObject()->render();
			$this->_commandChain->run('onAfterApplicationRender', $args);
		}

		return $args['result'];
	}
	
	/**
	 * Proxy the application close() method
	 *
	 * @param	int	Exit code
	 * @return	none|false The value returned by the proxied method, false in error case.
	 */
	function close( $code = 0 ) 
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name'] = get_class($this);
		$args['code']		= $code;
		
		if($this->_commandChain->run('onBeforeApplicationExit', $args) === true) {
			$this->getObject()->close($args['code']);
		}

		return false;
	}
	
	/**
	 * Proxy the application redirect() method
	 *
	 * @param	string	$url	The URL to redirect to.
	 * @param	string	$msg	An optional message to display on redirect.
	 * @param	string  $msgType An optional message type.
	 * @return	none|false The value returned by the proxied method, false in error case.
	 * @see		JApplication::enqueueMessage()
	 */
	public function redirect( $url, $msg = '', $msgType = 'message' )
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name']   = get_class($this);
		$args['url']          = $url;
		$args['message']      = $msg;
		$args['message_type'] = $msgType;
		
		if($this->_commandChain->run('onBeforeApplicationRedirect', $args) === true) {
			$this->getObject()->redirect($args['url'], $args['msg'], $args['msgType']);
		}

		return false;
	}
	
	/**
	 * Proxy the application login() method
	 *
	 * @param	array 	Array( 'username' => string, 'password' => string )
	 * @param	array 	Array( 'remember' => boolean )
	 * @return	mixed|false The value returned by the proxied method, false in error case.
	 */
	public function login($credentials, array $options = array())
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name']  = get_class($this);
		$args['credentials'] = $credentials;
		$args['options']     = $options;
		
		if($this->_commandChain->run('onBeforeApplicationLogin', $args) === true) {
			$args['result'] = $this->getObject()->login($args['credentials'], $args['options']);
			$this->_commandChain->run('onAfterApplicationLogin', $args);
		}
		
		return $args['result'];
	}
	
	/**
	 * Proxy the application logout() method
	 *
	 * @param 	int 	$userid   The user to load - Can be an integer or string - If string, it is converted to ID automatically
	 * @param	array 	$options  Array( 'clientid' => array of client id's )
	 * @return	mixed|false The value returned by the proxied method, false in error case.
	 */
	public function logout($userid = null, array $options = array())
	{
		//Create the arguments object
		$args = new ArrayObject();
		$args['class_name']  = get_class($this);
		$args['credentials'] = array('userid' => $userid);
		$args['options']     = $options;
		
		if($this->_commandChain->run('onBeforeApplicationLogout', $args) === true) {
			$args['result'] = $this->getObject()->logout($args['credentials']['userid'], $args['options']);
			$this->_commandChain->run('onAfterApplicationLogout', $args);
		}
		
		return $args['result'];
	}
}