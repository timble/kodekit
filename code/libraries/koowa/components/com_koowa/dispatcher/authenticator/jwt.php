<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Jwt Dispatcher Authenticator
 *
 * A token MAY contain and additional 'user' claim which contains a JSON hash of user field key and values to set on
 * the user.
 *
 * Supported fields :
 *
 * - fullname
 * - email
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class ComKoowaDispatcherAuthenticatorJwt extends KDispatcherAuthenticatorJwt
{
    /**
     * Options used when logging in the user
     *
     * @var boolean
     */
    protected $_options;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_options = KObjectConfig::unbox($config->options);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'secret'  => JFactory::getConfig()->get('secret'),
            'max_age' => JFactory::getConfig()->get('lifetime'),
            'options' => array(
                'action'       => JFactory::getApplication()->isSite() ? 'core.login.site' : 'core.login.admin',
                'autoregister' => false
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Log the user in
     *
     * @param string $username
     * @param array  $data
     * @return boolean
     */
    protected function _loginUser($username, $data = array())
    {
        $data['username'] = $username;
        $options = $this->_options;

        JPluginHelper::importPlugin('user');
        $results = JFactory::getApplication()->triggerEvent('onUserLogin', array($data, $options));

        // The user is successfully logged in. Refresh the current user.
        if (in_array(false, $results, true) == false)
        {
            parent::_loginUser($username);
            return true;
        }

        return false;
    }
}