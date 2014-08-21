<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Toolbar Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar
 */
interface KControllerToolbarInterface extends IteratorAggregate, Countable
{
    /**
     * Get the toolbar's name
     *
     * @return string
     */
    public function getName();

    /**
     * Add a command by name
     *
     * @param   string	$name    The command name
     * @param	array   $config  An optional associative array of configuration settings
     * @return  KControllerToolbarCommandInterface  The command object that was added
     */
    public function addCommand($name, $config = array());

    /**
     * Get a command by name
     *
     * @param string $name  The command name
     * @param array $config  An optional associative array of configuration settings
     * @return KControllerToolbarCommandInterface|boolean A toolbar command if found, false otherwise.
     */
    public function getCommand($name, $config = array()) ;

    /**
     * Check if a command exists
     *
     * @param string $name  The command name
     * @return boolean True if the command exists, false otherwise.
     */
    public function hasCommand($name);

    /**
     * Get the list of commands
     *
     * @return  array
     */
    public function getCommands();
}
