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
 * Object Decorator Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object\Decorator
 */
interface ObjectDecoratorInterface
{
    /**
     * Get the decorated object
     *
     * @return object The decorated object
     */
    public function getDelegate();

    /**
     * Set the decorated object
     *
     * @param   object $delegate The object to decorate
     * @return  ObjectDecoratorInterface
     * @throws  \InvalidArgumentException If the delegate is not an object
     */
    public function setDelegate($delegate);

    /**
     * Get a list of all the available methods
     *
     * This function returns an array of all the public methods, both native and mixed.
     *
     * @return array An array
     */
    public function getMethods();

    /**
     * Decorate Notifier
     *
     * This function is called when an object is being decorated. It will get the delegate passed in.
     *
     * @param  object $delegate The object being decorated
     * @throws \InvalidArgumentException If the delegate is not an object
     * @return void
     */
    public function onDecorate($delegate);
}
