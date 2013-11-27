<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Permissible Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class KControllerBehaviorPermissible extends KControllerBehaviorAbstract
{
    /**
     * The permission object
     *
     * @var mixed An object that implements KControllerPermissionInterface, KObjectIdentifierInterface or valid
     * identifier string
     */
    protected $_permission;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_permission = $config->permission;
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
            'priority'   => self::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * Only handles before.action commands to check authorization rules.
     *
     * @param   string          $name     The command name
     * @param   KCommandInterface $context  The command context
     * @throws  KControllerExceptionForbidden       If the user is authentic and the actions is not allowed.
     * @throws  KControllerExceptionUnauthorized    If the user is not authentic and the action is not allowed.
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function execute($name, KCommandInterface $context)
    {
        $parts = explode('.', $name);

        if($parts[0] == 'before')
        {
            $action = $parts[1];

            if($this->canExecute($action) === false)
            {
                if(JFactory::getUser()->guest) {
                    throw new KControllerExceptionUnauthorized('Action '.ucfirst($action).' Not Allowed');
                } else {
                    throw new KControllerExceptionForbidden('Action '.ucfirst($action).' Not Allowed');
                }
            }
        }

        return true;
    }

    /**
     * Check if an action can be executed
     *
     * @param   string  $action Action name
     * @return  boolean True if the action can be executed, otherwise FALSE.
     */
    public function canExecute($action)
    {
        $result = false;

        $method = 'can' . ucfirst($action);

        if (($permission = $this->getPermission()) && method_exists($permission, $method))
        {
            $result = $permission->$method();
        }
        elseif ($mixer = $this->getMixer())
        {
            $actions = $mixer->getActions();
            $actions = array_flip($actions);
            $result  = isset($actions[$action]);
        }

        return $result;
    }

    /**
     * Permission getter.
     *
     * @return KControllerPermissionInterface|null The permission object, null if permission is not set.
     */
    public function getPermission()
    {
        if ($this->_permission && !$this->_permission instanceof KControllerPermissionInterface)
        {
            if (!$this->_permission instanceof KObjectIdentifier)
            {
                $this->setPermission($this->_permission);
            }

            $classname = $this->_permission->classname;
            $config    = new KObjectConfig(array('mixer' => $this->getMixer()));

            $this->_permission = new $classname($config);
        }

        return $this->_permission;
    }

    /**
     * Permission setter.
     *
     * @param mixed $permission An object that implements KControllerPermissionInterface, KObjectIdentifier or a
     *                          valid identifier string.
     *
     * @return $this
     */
    public function setPermission($permission)
    {
        if (!$permission instanceof KControllerPermissionInterface && !$permission instanceof KObjectIdentifierInterface)
        {
            $permission = $this->getIdentifier($permission);
        }

        $this->_permission = $permission;

        return $this;
    }

    /**
     * Mixin Notifier
     *
     * This function is called when the mixin is being mixed. It will get the mixer passed in.
     *
     * @param KObjectMixable $mixer The mixer object
     * @return void
     */
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        if (!$this->getPermission())
        {
            $permission       = clone $mixer->getIdentifier();
            $permission->path = array('controller', 'permission');
            $this->setPermission($permission);
        }

        $mixer->mixin($this->getPermission());
    }

    /**
     * Get an object handle
     *
     * Force the object to be enqueue in the command chain.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}