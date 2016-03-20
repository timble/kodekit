<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Abstract Loader Adapter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Class\Locator
 */
abstract class ClassLocatorAbstract implements ClassLocatorInterface
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = '';

    /**
     * Locator namespaces
     *
     * @var array
     */
    protected $_namespaces = array();

    /**
     * Constructor.
     *
     * @param  array  $config An optional array with configuration options.
     */
    public function __construct( $config = array())
    {
        if(isset($config['namespaces']))
        {
            $namespaces = (array) $config['namespaces'];
            foreach($namespaces as $namespace => $path) {
                $this->registerNamespace($namespace, $path);
            }
        }
    }

    /**
     * Register a namespace
     *
     * @param  string       $namespace
     * @param  string|array $path(s) The location of the namespace
     * @return ClassLocatorInterface
     */
    public function registerNamespace($namespace, $path)
    {
        $namespace = trim($namespace, '\\');
        $this->_namespaces[$namespace] = (array) $path;

        krsort($this->_namespaces, SORT_STRING);

        return $this;
    }

    /**
     * Get a namespace path
     *
     * @param string $namespace The namespace
     * @return array|false The namespace path(s) or FALSE if the namespace does not exist.
     */
    public function getNamespacePath($namespace)
    {
        $namespace = trim($namespace, '\\');
        return isset($this->_namespaces[$namespace]) ?  $this->_namespaces[$namespace] : false;
    }

    /**
     * Get the registered namespaces
     *
     * @return array An array with namespaces as keys and path as value
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }

    /**
     * Get locator name
     *
     * @return string
     */
    public static function getName()
    {
        return static::$_name;
    }
}
