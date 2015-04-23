<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Loader Adapter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Class\Locator
 */
abstract class KClassLocatorAbstract implements KClassLocatorInterface
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
     * @param  string $namespace
     * @param  string $path The location of the namespace
     * @return KClassLocatorInterface
     */
    public function registerNamespace($namespace, $path)
    {
        $namespace = trim($namespace, '\\');
        $this->_namespaces[$namespace] = $path;

        krsort($this->_namespaces, SORT_STRING);

        return $this;
    }

    /**
     * Get a namespace path
     *
     * @param string $namespace The namespace
     * @return string|false The namespace path or FALSE if the namespace does not exist.
     */
    public function getNamespace($namespace)
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
