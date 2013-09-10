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
     * @var KControllerPermissionInterface
     */
    protected $_permission;

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
            'priority'   => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * Only handles before.action commands to check authorization rules.
     *
     * @param   string          $name     The command name
     * @param   KCommandContext $context  The command context
     * @throws  KControllerExceptionForbidden       If the user is authentic and the actions is not allowed.
     * @throws  KControllerExceptionUnauthorized    If the user is not authentic and the action is not allowed.
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function execute($name, KCommandContext $context)
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
        $method  = 'can'.ucfirst($action);
        $methods = $this->getMixer()->getMethods();

        if (!isset($methods[$method]))
        {
            $actions = $this->getActions();
            $actions = array_flip($actions);

            $result = isset($actions[$action]);
        }
        else $result = $this->$method();

        return $result;
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

        //Mixin the permission
        $permission       = clone $mixer->getIdentifier();
        $permission->path = array('controller', 'permission');

        if($permission !== $this->getPermission()) {
            $this->setPermission($mixer->mixin($permission));
        }
    }

    /**
     * Get the permission
     *
     * @return KControllerPermissionInterface
     */
    public function getPermission()
    {
        return $this->_permission;
    }

    /**
     * Set the permission
     *
     * @param  KControllerPermissionInterface $permission The controller permission object
     * @return KControllerBehaviorPermissible
     */
    public function setPermission(KControllerPermissionInterface $permission)
    {
        $this->_permission = $permission;
        return $this;
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