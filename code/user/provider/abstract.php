<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract User Provider
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User\Provider
 */
class KUserProviderAbstract extends KObject implements KUserProviderInterface
{
    /**
     * The list of users by identifier
     *
     * @var array
     */
    protected $_users = array();

    /**
     * Constructor
     *
     * The user array is a hash where the keys are user identifier and the values are an array of attributes:
     * 'password', 'enabled', and 'roles' etc. The user identifiers should be unique.
     *
     * @param   KObjectConfig $config  An optional KObjectConfig object with configuration options
     * @return  KUserProviderAbstract
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create the users
        foreach($config->users as $identifier => $data) {
            $this->setUser($this->create($data));
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'users' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Load the user for the given username or identifier, fetching it from data store if it doesn't exist yet.
     *
     * @param string $identifier A unique user identifier, (i.e a username or email address)
     * @param bool  $refresh     If TRUE and the user has already been loaded it will be re-loaded.
     * @return KUserInterface Returns a UserInterface object.
     */
    public function getUser($identifier, $refresh = false)
    {
        $result = null;

        //Fetch a user from the backend
        if($refresh || !$this->isLoaded($identifier))
        {
            $this->fetch($identifier, $refresh);

            if($this->isLoaded($identifier)) {
                $result = $this->_users[$identifier];
            }
        }

        return  $result;
    }

    /**
     * Store user object in the provider
     *
     * @param KUserInterface $user
     * @return KUserProviderAbstract
     */
    public function setUser(KUserInterface $user)
    {
        $this->_users[$user->getId()] = $user;
        return true;
    }

    /**
     * Fetch the user for the given user identifier from the data store
     *
     * @param string|array $identifier A unique user identifier, (i.e a username or email address)
     *                                 or an array of identifiers
     * @param bool  $refresh     If TRUE and the user has already been fetched it will be re-fetched.
     * @return boolean
     */
    public function fetch($identifier, $refresh = false)
    {
        $identifiers = (array) $identifier;

        foreach($identifiers as $identifier)
        {
            $data = array(
                'id'         => $identifier,
                'authentic'  => false
            );

            $this->setUser($this->create($data));
        }

        return true;
    }

    /**
     * Create a user object
     *
     * @param array $data An associative array of user data
     * @return KUserInterface     Returns a UserInterface object
     */
    public function create($data)
    {
        $user = $this->getObject('user.default', array('data' => $data));
        return $user;
    }

    /**
     * Check if a user has already been loaded for a given user identifier
     *
     * @param $identifier
     * @return boolean TRUE if a user has already been loaded. FALSE otherwise
     */
    public function isLoaded($identifier)
    {
        return isset($this->_users[$identifier]);
    }
}