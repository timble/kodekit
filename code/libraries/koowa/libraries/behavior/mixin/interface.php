<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Behavior Mixin Interface
 *
 * Behaviors are added in FIFO order during construction. Behaviors are added by name and, at runtime behaviors
 * cannot be overridden by attaching a behaviors with the same.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Behavior\Mixin
 */
interface KBehaviorMixinInterface
{
    /**
     * Add a behavior
     *
     * @param   mixed $behavior An object that implements BehaviorInterface, an ObjectIdentifier
     *                            or valid identifier string
     * @param   array $config An optional associative array of configuration settings
     * @throws UnexpectedValueException
     * @return  KObject The mixer object
     */
    public function addBehavior($behavior, $config = array());

    /**
     * Check if a behavior exists
     *
     * @param   string  $name The name of the behavior
     * @return  boolean TRUE if the behavior exists, FALSE otherwise
     */
    public function hasBehavior($name);

    /**
     * Get a behavior by name
     *
     * @param  string  $name   The behavior name
     * @return KBehaviorInterface
     */
    public function getBehavior($name);

    /**
     * Gets the behaviors of the table
     *
     * @return array An associative array of table behaviors, keys are the behavior names
     */
    public function getBehaviors();
}