<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Discoverable Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class KControllerBehaviorDiscoverable extends KControllerBehaviorAbstract
{
    /**
     * Get a list of allowed actions
     *
     * @param KCommand $context
     * @return  string    The allowed actions; e.g., `GET, POST [add, edit, cancel, save], PUT, DELETE`
     */
	protected function _actionOptions(KCommand $context)
	{
	    $methods = array();

        //Remove GET actions
        $actions = array_diff($this->getActions(), array('browse', 'read', 'display'));

        //Authorize the action
        foreach($actions as $key => $action)
        {
            //Find the mapped action if one exists
            if (isset( $this->_action_map[$action] )) {
                $action = $this->_action_map[$action];
            }

            //Check if the action can be executed
            if ($this->getBehavior('permissible')->execute('before.'.$action, $context) === false) {
                unset($actions[$key]);
            }
        }

        //Sort the action alphabetically.
        sort($actions);

        //Retrieve HTTP methods
        foreach(array('get', 'put', 'delete', 'post', 'options') as $method)
        {
            if(in_array($method, $actions)) {
                $methods[strtoupper($method)] = $method;
            }
        }

        //Retrieve POST actions
        if(in_array('post', $methods))
        {
            $actions = array_diff($actions, array('get', 'put', 'delete', 'post', 'options'));
            $methods['POST'] = array_diff($actions, $methods);
        }

        //Render to string
        $result = implode(', ', array_keys($methods));

        foreach($methods as $method => $actions)
        {
           if(is_array($actions) && !empty($actions)) {
               $result = str_replace($method, $method.' ['.implode(', ', $actions).']', $result);
           }
        }

        $context->headers = array('Allow' => $result);
	}
}
