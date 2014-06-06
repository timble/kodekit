<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Bootstrapper Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Bootstrapper
 */
interface KObjectBootstrapperInterface extends KObjectHandlable
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
     * Perform the bootstrapping
     *
     * @return void
     */
    public function bootstrap();

    /**
     * Get the object manager
     *
     * @return KObjectManagerInterface
     */
    public function getObjectManager();

    /**
     * Get the class loader
     *
     * @return KClassLoaderInterface
     */
    public function getClassLoader();

    /**
     * Get the priority of the bootstrapper
     *
     * @return  integer The priority level
     */
    public function getPriority();
}