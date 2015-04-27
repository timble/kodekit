<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Koowa constant, if true koowa is loaded
 */
define('KOOWA', 1);

/**
 * Koowa Framework Loader
 *
 * Loads classes and files, and provides metadata for Koowa such as version info
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library
 */
class Koowa
{
    /**
     * Koowa version
     *
     * @var string
     */
    const VERSION = '2.2.0-dev';

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
            $this->_vendor_path = $this->_root_path.'/libraries/vendor';
        }

        //Load the legacy functions
        require_once dirname(__FILE__) . '/legacy.php';

        //Setup the loader
        require_once dirname(__FILE__).'/class/loader.php';

        if (!isset($config['class_loader'])) {
            $config['class_loader'] = KClassLoader::getInstance($config);
        }

        //Setup the factory
        $manager = KObjectManager::getInstance($config);

        //Register the component class locator
        $manager->getClassLoader()->registerLocator(new KClassLocatorComponent(
            array(
                'namespaces' => array(
                    '\\'    => $this->_base_path.'/components',
                    'Koowa' => dirname(dirname(__FILE__))
                )
            )
        ));

        //Register the component object locator
        $manager->registerLocator('lib:object.locator.component');

        //Register the composer class locator
        if(file_exists($this->getVendorPath()))
        {
            $manager->getClassLoader()->registerLocator(new KClassLocatorComposer(
                array(
                    'vendor_path' => $this->getVendorPath()
                )
            ));
        }

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
     * @return Koowa
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
     * @return Koowa
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
     * @return Koowa
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
