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
 * Loader Adapter Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Class\Locator
 */
interface ClassLocatorInterface
{
    /**
     * Get locator name
     *
     * @return string
     */
    public static function getName();

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string  $class    The class name
     * @return string|boolean    Returns the path on success FALSE on failure
     */
    public function locate($class);

    /**
     * Register a namespace
     *
     * @param  string       $namespace
     * @param  string|array $path(s) The location of the namespace
     * @return ClassLocatorInterface
     */
    public function registerNamespace($namespace, $path);

    /**
     * Get a namespace path
     *
     * @param string $namespace The namespace
     * @return array|false The namespace path(s) or FALSE if the namespace does not exist.
     */
    public function getNamespacePath($namespace);

    /**
     * Get the registered namespaces
     *
     * @return array An array with namespaces as keys and path as value
     */
    public function getNamespaces();
}
