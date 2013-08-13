<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Callback Mixin
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Mixin
 */
class KObjectMixinCallback extends KObjectMixinAbstract
{
 	/**
 	 * Array of callbacks
 	 *
 	 * $var array
 	 */
	protected $_callbacks = array();

	/**
     * Config passed to the callbacks
     *
     * @var array
     */
   	protected $_params = array();

    /**
     * Execute the named callbacks
     *
     * @param string   $name  The callback name
     * @return void
     */
    public function executeCallbacks( $name )
    {
        $result = true;

        $callbacks = $this->getCallbacks($name);
        $params    = $this->_params[$name];

        foreach($callbacks as $key => $callback)
        {
            $param = $params[$key];

            if(is_array($param) && !empty($params)) {
                call_user_func_array($callback, $params);
            } else {
                call_user_func($callback);
            }
        }
    }

	/**
 	 * Register a named callback
 	 *
 	 * If the callback has already been registered. It will not be re-registered.
 	 *
 	 * @param  	string      	$name       The callback name to register the callback for
 	 * @param 	callable		$callback   The callback function to register
 	 * @param   array|object    An associative array of config parameters or a KConfig object
     * @throws  InvalidArgumentException If the callback is not a callable
 	 * @return  KObject	The mixer object
 	 */
	public function registerCallback($name, $callback, $params = array())
	{
        if (!is_callable($callback))
        {
            throw new InvalidArgumentException(
                'The callback must be a callable, "'.gettype($callback).'" given.'
            );
        }

		$params = (array) KConfig::unbox($params);
        $name   = strtolower($name);

        if (!isset($this->_callbacks[$name]) )
        {
            $this->_callbacks[$name] = array();
            $this->_params[$name]   = array();
        }

        //Don't re-register names
        $index = array_search($callback, $this->_callbacks[$name], true);

        if ( $index === false )
        {
            $this->_callbacks[$name][] = $callback;
            $this->_params[$name][]    = $params;
        }
        else $this->_params[$name][$index] = array_merge($this->_params[$name][$index], $params);

		return $this->getMixer();
	}

	/**
 	 * Unregister a named callback
 	 *
 	 * @param  	string|array	$name       The callback name to unregister the callback from
 	 * @param 	callback		$callback   The callback function to unregister
 	 * @return  KObject The mixer object
 	 */
	public function unregisterCallback($name, $callback)
	{
        $name = strtolower($name);

        if (isset($this->_callbacks[$name]) )
        {
            $key = array_search($callback, $this->_callbacks[$name], true);
            unset($this->_callbacks[$name][$key]);
            unset($this->_params[$name][$key]);
        }

		return $this->getMixer();
	}

    /**
     * Get the registered callbacks by name
     *
     * @param  	string	$name The callback name to return the callbacks for
     * @return  array	A list of registered callbacks
     */
    public function getCallbacks($name)
    {
        $result = array();
        $name   = strtolower($name);

        if (isset($this->_callbacks[$name]) ) {
            $result = $this->_callbacks[$name];
        }

        return $result;
    }
}
