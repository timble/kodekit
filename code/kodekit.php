<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

/**
 * Kodekit constant, if true Kodekit is loaded
 */
define('KODEKIT', 1);

use Kodekit\Library;

/**
 * Kodekit Loader
 *
 * Loads classes and files, and provides metadata for Kodekit such as version info
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library
 */
class Kodekit
{
    /**
     * Kodekit version
     *
     * @var string
     */
    const VERSION = '3.0-beta1';

    /**
     * Debug state
     *
     * @var boolean
     */
    protected $_debug;

    /**
     * Cache state
     *
     * @var boolean
     */
    protected $_cache;

    /**
     * The root path
     *
     * @var string
     */
    protected $_root_path;

    /**
     * The base path
     *
     * @var string
     */
    protected $_base_path;

    /**
     * The vendor path
     *
     * @var string
     */
    protected $_vendor_path;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     *
     * @param  array  $config An optional array with configuration options.
     */
    final private function __construct($config = array())
    {
        //Initialize the debug state
        if(isset($config['debug'])) {
            $this->_debug = $config['debug'];
        } else {
            $this->_debug = false;
        }

        //Initialize the debug state
        if(isset($config['cache'])) {
            $this->_cache = $config['cache'];
        } else {
            $this->_cache = false;
        }

        //Initialize the root path
        if(isset($config['root_path'])) {
            $this->_root_path = $config['root_path'];
        } else {
            $this->_root_path = realpath($_SERVER['DOCUMENT_ROOT']);
        }

        //Initialize the base path
        if(isset($config['base_path'])) {
            $this->_base_path = $config['base_path'];
        } else {
            $this->_base_path = $this->_root_path;
        }

        //Initialize the vendor path
        if(isset($config['vendor_path'])) {
            $this->_vendor_path = $config['vendor_path'];
        } else {
            $this->_vendor_path = $this->_root_path.'/vendor';
        }

        //Load the legacy functions
        require_once dirname(__FILE__) . '/legacy.php';

        //Setup the loader
        require_once dirname(__FILE__).'/class/loader.php';

        if (!isset($config['class_loader'])) {
            $config['class_loader'] = Library\ClassLoader::getInstance($config);
        }

        //Setup the factory
        $manager = Library\ObjectManager::getInstance($config);

        //Register the component class locator
        $manager->getClassLoader()->registerLocator(new Library\ClassLocatorComponent());

        //Register the component object locator
        $manager->registerLocator('lib:object.locator.component');

        //Register the composer class locator
        if(file_exists($this->getVendorPath()))
        {
            $manager->getClassLoader()->registerLocator(new Library\ClassLocatorComposer(
                array(
                    'vendor_path' => $this->getVendorPath()
                )
            ));
        }

        //Register the PSR locator
        $manager->getClassLoader()->registerLocator(new Library\ClassLocatorPsr);

        //Warm-up the stream factory
        $manager->getObject('lib:filesystem.stream.factory');
    }

    /**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone() { }

    /**
     * Singleton instance
     *
     * @param  array  $config An optional array with configuration options.
     * @return Kodekit
     */
    final public static function getInstance($config = array())
    {
        static $instance;

        if ($instance === NULL) {
            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Get the framework version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Get vendor path
     *
     * @return string
     */
    public function getVendorPath()
    {
        return $this->_vendor_path;
    }

    /**
     * Get root path
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->_root_path;
    }

    /**
     * Get base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_base_path;
    }

    /**
     * Enable or disable debug
     *
     * @param bool $debug True or false.
     * @return Kodekit
     */
    public function setDebug($debug)
    {
        return $this->_debug = (bool) $debug;
    }

    /**
     * Check if debug is enabled
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * Enable or disable the cache
     *
     * @param bool $cache True or false.
     * @return Kodekit
     */
    public function setCache($cache)
    {
        return $this->_cache = (bool) $cache;
    }

    /**
     * Check if caching is enabled
     *
     * @return bool
     */
    public function isCache()
    {
        return $this->_cache;
    }
}