<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

require_once dirname(__FILE__) . '/../locator/interface.php';
require_once dirname(__FILE__) . '/../locator/abstract.php';
require_once dirname(__FILE__) . '/../locator/koowa.php';
require_once dirname(__FILE__) . '/../registry.php';
require_once dirname(__FILE__) . '/interface.php';

/**
 * Loader
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
class KClassLoader implements KClassLoaderInterface
{
    /**
     * The file container
     *
     * @var array
     */
    protected $_registry = null;

    /**
     * Adapter list
     *
     * @var array
     */
    protected $_locators = array();

    /**
     * Prefix map
     *
     * @var array
     */
    protected $_prefix_map = array();

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     */
    final private function __construct($config = array())
    {
        //Create the class registry
        $this->_registry = new KClassRegistry();

        if(isset($config['cache_prefix'])) {
            $this->_registry->setCachePrefix($config['cache_prefix']);
        }

        if(isset($config['cache_enabled'])) {
            $this->_registry->enableCache($config['cache_enabled']);
        }

        //Add the koowa class loader
        $this->registerLocator(new KClassLocatorKoowa(
            array('basepaths' => array('*' => dirname(dirname(dirname(__FILE__)))))
        ));

        //Auto register the loader
        $this->register();
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
     * @return KClassLoader
     */
    public static function getInstance($config = array())
    {
        static $instance;

        if ($instance === NULL) {
            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @return void
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);

        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
    }

    /**
     * Unregisters the loader with the PHP autoloader.
     *
     * @return void
     *
     * @see spl_autoload_unregister();
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }


    /**
     * Get the class registry object
     *
     * @return object KClassRegistry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Register a class locator
     *
     * @param  KClassLocatorInterface $locator
     * @return void
     */
    public function registerLocator(KClassLocatorInterface $locator)
    {
        $this->_locators[$locator->getType()]     = $locator;
        $this->_prefix_map[$locator->getPrefix()] = $locator->getType();
    }

    /**
     * Get a registered class locator based on his type
     *
     * @param string $type The locator type
     * @return KClassLocatorInterface|null  Returns the object locator or NULL if it cannot be found.
     */
    public function getLocator($type)
    {
        $result = null;

        if(isset($this->_locators[$type])) {
            $result = $this->_locators[$type];
        }

        return $result;
    }

	/**
     * Get the registered adapters
     *
     * @return array
     */
    public function getLocators()
    {
        return $this->_locators;
    }

    /**
     * Load a class based on a class name
     *
     * @param string  $class    The class name
     * @param string  $basepath The basepath
     * @return boolean  Returns TRUE on success throws exception on failure
     */
    public function loadClass($class, $basepath = null)
    {
        $result = false;

        //Extra filter added to circumvent issues with Zend Optimiser and strange classname.
        if((ctype_upper(substr($class, 0, 1)) || (strpos($class, '.') !== false)))
        {
            //Pre-empt further searching for the named class or interface.
            //Do not use autoload, because this method is registered with
            //spl_autoload already.
            if (!class_exists($class, false) && !interface_exists($class, false))
            {
                //Get the path
                $path = $this->findPath( $class, $basepath );

                if ($path !== false) {
                    $result = $this->loadFile($path);
                }
            }
            else $result = true;
        }

        return $result;
    }

	/**
     * Load a class based on an identifier
     *
     * @param string|object The identifier or identifier object
     * @return boolean      Returns TRUE on success throws exception on failure
     */
    public function loadIdentifier($identifier)
    {
        $result = false;

        $identifier = KService::getIdentifier($identifier);

        //Get the path
        $path = $identifier->filepath;

        if ($path !== false) {
            $result = $this->loadFile($path);
        }

        return $result;
    }

    /**
     * Load a class based on a path
     *
     * @param string	$path The file path
     * @return boolean  Returns TRUE on success throws exception on failure
     */
    public function loadFile($path)
    {
        $result = false;

        //Don't re-include files and stat the file if it exists.
        //Realpath is needed to resolve symbolic links.
        if (!in_array(realpath($path), get_included_files()) && file_exists($path))
        {
            if ($included = include $path) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get the path based on a class name
     *
     * @param string	$class      The class name
     * @param string    $basepath   The basepath
     * @return string   Returns canonicalized absolute pathname
     */
    public function findPath($class, $basepath = null)
    {
        static $base;

        //Switch the base
        $base = $basepath ? $basepath : $base;

        if(!$this->_registry->offsetExists($base.'-'.(string) $class))
        {
            $result = false;

            $word  = preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $class);
            $parts = explode(' ', $word);

            if(isset($this->_prefix_map[$parts[0]]))
            {
                $result = $this->_locators[$this->_prefix_map[$parts[0]]]->locate( $class, $basepath);

                if ($result !== false)
                {
                   //Get the canonicalized absolute pathname
                   $path = realpath($result);
                   $result = $path !== false ? $path : $result;
                }

                $this->_registry->offsetSet($base.'-'.(string) $class, $result);
            }

        } else $result = $this->_registry->offsetGet($base.'-'.(string)$class);

        return $result;
    }
}
