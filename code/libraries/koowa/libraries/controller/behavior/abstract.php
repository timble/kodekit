<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Behavior
 */
abstract class KControllerBehaviorAbstract extends KBehaviorAbstract
{
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
            foreach($this->getMethods() as $method)
            {
                if(substr($method, 0, 7) == '_action') {
                    $methods[strtolower(substr($method, 7))] = $this;
                }
            }
        }

        return $methods;
    }

    /**
     * Command handler
     *
     * @param KCommandInterface         $command    The command
     * @param KCommandChainInterface    $chain      The chain executing the command
     * @return mixed If a handler breaks, returns the break condition. Returns the result of the handler otherwise.
     */
    public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $parts  = explode('.', $command->getName());
        $method = '_'.$parts[0].ucfirst($parts[1]);

        if($parts[0] == 'action') {
            $result = $this->$method($command);
        } else {
            $result = parent::execute($command, $chain);
        }

        return $result;
    }
}
