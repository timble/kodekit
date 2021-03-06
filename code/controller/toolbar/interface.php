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
 * Controller Toolbar Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller\Toolbar
 */
interface ControllerToolbarInterface extends \IteratorAggregate, \Countable
{
    /**
     * Get the toolbar's name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the toolbar's title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Add a command by name
     *
     * @param   string	$name    The command name
     * @param	array   $config  An optional associative array of configuration settings
     * @return  ControllerToolbarCommandInterface  The command object that was added
     */
    public function addCommand($name, $config = array());

    /**
     * Get a command by name
     *
     * @param string $name  The command name
     * @param array $config  An optional associative array of configuration settings
     * @return ControllerToolbarCommandInterface|boolean A toolbar command if found, false otherwise.
     */
    public function getCommand($name, $config = array()) ;

    /**
     * Check if a command exists
     *
     * @param string $name  The command name
     * @return boolean True if the command exists, false otherwise.
     */
    public function hasCommand($name);
}
