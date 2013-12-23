<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

require_once dirname(__FILE__).'/interface.php';
require_once dirname(__FILE__).'/locator/interface.php';
require_once dirname(__FILE__).'/locator/abstract.php';
require_once dirname(__FILE__).'/locator/koowa.php';
require_once dirname(__FILE__).'/registry/interface.php';
require_once dirname(__FILE__).'/registry/registry.php';

/**
 * Loader
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
class KClassLoader implements KClassLoaderInterface
{
    /**
     * The class locators
     *
     * @var array
     */
    protected $_locators = array();

    /**
     * The class container
     *
     * @var array
     */
    protected $_registry = null;

    /**
     * Class aliases
     *
     * @var    array
     */
    protected $_aliases = array();

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
        if(isset($config['cache_enabled']) && $config['cache_enabled'])
        {
            $this->_registry = new KClassRegistryCache();

            if(isset($config['cache_prefix'])) {
                $this->_registry->setNamespace($config['cache_prefix']);
            }
        }
        else $this->_registry = new KClassRegistry();

        //Add the koowa class loader
        $this->registerLocator(new KClassLocatorKoowa(
            array('basepaths' => array('*' => dirname(dirname(__FILE__))))
        ));

        //Auto register the loader
        $this->register();
    }

    /**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone()
    {
        throw new Exception("An instance of KClassLoader cannot be cloned.");
    }

    /**
     * Singleton instance
     *
     * @param  array  $config An optional array with configuration options.
     * @return KClassLoader
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
     * Registers this instance as an autoloader.
     *
     * @param boolean $prepend Whether to prepend the autoloader or not
     * @return void
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'load'), true, $prepend);

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
        spl_autoload_unregister(array($this, 'load'));
    }

    /**
     * Load a class based on a class name
     *
     * @param string  $class    The class name
     * @param string  $basepath The basepath
     * @return boolean  Returns TRUE on success throws exception on failure
     */
    public function load($class, $basepath = null)
    {
        $result = true;

        if(!$this->isDeclared($class))
        {
            //Get the path
            $path = $this->find( $class, $basepath );

            if ($path !== false)
            {
                if (!in_array($path, get_included_files()) && file_exists($path)){
                    require $path;
                } else {
                    $result = false;
                }

            }
            else $result = false;
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
    public function find($class, $basepath = null)
    {
        static $base;

        //Switch the base
        $base = $basepath ? $basepath : $base;

        //Recursively resolve the real class if an alias was passed
        while(array_key_exists((string) $class, $this->_aliases)) {
            $class = $this->_aliases[(string) $class];
        }

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
     * Register an alias for a class
     *
     * @param string  $class The original
     * @param string  $alias The alias name for the class.
     */
    public function registerAlias($class, $alias)
    {
        $alias = trim($alias);
        $class = trim($class);

        $this->_aliases[$alias] = $class;
    }

    /**
     * Get the registered alias for a class
     *
     * @param  string $class The class
     * @return array   An array of aliases
     */
    public function getAliases($class)
    {
        return isset($this->_aliases[$class]) ? $this->_aliases[$class] : array();
    }

    /**
     * Tells if a class, interface or trait exists.
     *
     * @param string $class
     * @return boolean
     */
    public function isDeclared($class)
    {
        return class_exists($class, false)
        || interface_exists($class, false)
        || (function_exists('trait_exists') && trait_exists($class, false));
    }
}
