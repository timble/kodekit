<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Abstract Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller\Behavior
 */
abstract class ControllerBehaviorAbstract extends BehaviorAbstract
{
    /**
     * The actions
     *
     * @var array
     */
    private $__actions = array();

    /**
     * Constructor.
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        foreach ($this->getMethods() as $method)
        {
            if (substr($method, 0, 7) == '_action') {
                $this->__actions[] = strtolower(substr($method, 7));
            }
        }
    }

    /**
     * Get the methods that are available for mixin based
     *
     * This function dynamically adds mixable methods of format _action[Action]
     *
     * @param  array $exclude   A list of methods to exclude
     * @return array An array of methods
     */
    public function getMixableMethods($exclude = array())
    {
        $methods = parent::getMixableMethods($exclude);

        if($this->isSupported())
        {
            foreach($this->__actions as $action) {
                $methods[$action] = $this;
            }
        }

        return $methods;
    }


    /**
     * Execute a mixed controller action by it's name
     *
     * If the method is an action defined by the behavior call _action[Method]
     *
     * @param  string  $method Method name
     * @param  array   $args   Array containing all the arguments for the original call
     * @return mixed
     * @see execute()
     */
    public function __call($method, $args)
    {
        //Handle action alias method
        if(in_array($method, $this->__actions))
        {
            if(isset($args[0]) && $args[0] instanceof CommandInterface)
            {
                $method = '_action'.ucfirst($method);
                return $this->$method($args[0]);
            }
        }

        return parent::__call($method, $args);
    }
}
