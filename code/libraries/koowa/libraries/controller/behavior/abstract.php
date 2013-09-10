<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */
/**
 * Abstract Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerBehaviorAbstract extends KBehaviorAbstract
{
	/**
	 * Command handler
	 *
	 * This function translates the command name to a command handler function of the format '_before[Command]' or
     * '_after[Command]. Command handler functions should be declared protected.
	 *
	 * @param 	string           $name	    The command name
	 * @param 	KCommandContext  $context 	The command context
	 * @return 	boolean
	 */
	public function execute($name, KCommandContext $context)
	{
        $this->setMixer($context->getSubject());

        $parts = explode('.', $name);
        if ($parts[0] == 'action')
        {
            $method = '_action' . ucfirst($parts[1]);

            if (method_exists($this, $method)) {
                return $this->$method($context);
            }
        }

        return parent::execute($name, $context);
	}

    /**
     * Get the methods that are available for mixin based
     *
     *  This function also dynamically adds a function of format _action[Action]
     *
     * @param KObjectMixable $mixer The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null)
    {
        $methods = parent::getMixableMethods($mixer);

        foreach($this->getMethods() as $method)
        {
            if(substr($method, 0, 7) == '_action') {
                $methods[strtolower(substr($method, 7))] = strtolower(substr($method, 7));
            }
        }

        return $methods;
    }
}
