<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Handler Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
interface KCommandHandlerInterface extends KObjectHandlable
{
    /**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

    /**
     * Execute the handler
     *
     * @param KCommandInterface         $command    The command
     * @param KCommandChainInterface    $chain      The chain executing the command
     * @return mixed|null If the handler breaks, returns the break condition. NULL otherwise.
     */
    public function execute(KCommandInterface $command, KCommandChainInterface $chain);

	/**
	 * Get the priority of the handler
	 *
	 * @return	integer The invoker priority
	 */
  	public function getPriority();
}
