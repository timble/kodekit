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
 * Template Engine Factory
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Engine
 */
class TemplateEngineFactory extends ObjectAbstract implements ObjectSingleton
{
    /**
     * Registered engines
     *
     * @var array
     */
    private $__engines;

    /**
     * Debug
     *
     * @var boolean
     */
    protected $_debug;

    /**
     * Caching enabled
     *
     * @var bool
     */
    protected $_cache;

    /**
     * Cache path
     *
     * @var string
     */
    protected $_cache_path;

    /**
     * Cache reload
     *
     * @var bool
     */
    protected $_cache_reload;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config Configuration options
     */
    public function __construct( ObjectConfig $config)
    {
        parent::__construct($config);

        //Set debug
        $this->setDebug($config->debug);

        //Set cache
        $this->setCache($config->cache, $config->cache_path, $config->cache_reload);

        //Register the engines
        $engines = ObjectConfig::unbox($config->engines);

        foreach ($engines as $key => $value)
        {
            if (is_numeric($key)) {
                $this->registerEngine($value);
            } else {
                $this->registerEngine($key, $value);
            }
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'debug'        => \Kodekit::getInstance()->isDebug(),
            'cache'        => \Kodekit::getInstance()->isCache(),
            'cache_path'   => '',
            'engines'      => array(
                'lib:template.engine.kodekit'
            ),
        ))->append(array(
            'cache_reload' => $config->debug,
        ));
    }
    /**
     * Create an engine
     *
     * Note that only paths ending with '.[type]' are supported. If the url is not a path we will assume the url is
     * the type. If no engine is registered for the specific file type a exception will be thrown.
     *
     * @param  string $url    The template url or engine type
     * @param  array $config  An optional associative array of configuration options
     * @throws \InvalidArgumentException If the path is not valid
     * @throws \RuntimeException         If the engine isn't registered
     * @throws \UnexpectedValueException If the engine object doesn't implement the TemplateEngineInterface
     * @return TemplateEngineInterface
     */
    public function createEngine($url, array $config = array())
    {
        //Find the file type
        if(!$type = pathinfo($url, PATHINFO_EXTENSION)) {
            $type = $url;
        }

        //Engine not supported
        if(!in_array($type, $this->getFileTypes()))
        {
            throw new \RuntimeException(sprintf(
                'Unable to find a template engine for the "%s" file format - did you forget to register it ?', $type
            ));
        }

        //Create the engine
        $identifier = $this->getEngine($type);
        $engine     = $this->getObject($identifier, $config);

        if(!$engine instanceof TemplateEngineInterface)
        {
            throw new \UnexpectedValueException(
                'Engine: '.get_class($engine).' does not implement TemplateEngineInterface'
            );
        }

        return $engine;
    }

    /**
     * Register an engine
     *
     * Function prevents from registering the engine twice
     *
     * @param string $identifier A engine identifier string
     * @param  array $config  An optional associative array of configuration options
     * @throws \UnexpectedValueException
     * @return bool Returns TRUE on success, FALSE on failure.
     */
    public function registerEngine($identifier, array $config = array())
    {
        $result = false;

        $identifier = $this->getIdentifier($identifier);
        $class      = $this->getObject('manager')->getClass($identifier);

        if(!$class || !array_key_exists(__NAMESPACE__.'\TemplateEngineInterface', class_implements($class)))
        {
            throw new \UnexpectedValueException(
                'Engine: '.$identifier.' does not implement TemplateEngineInterface'
            );
        }

        $types = $class::getFileTypes();

        if (!empty($types))
        {
            foreach($types as $type)
            {
                if(!$this->isRegistered($type))
                {
                    $identifier->getConfig()->merge($config)->append(array(
                        'debug'         => $this->getConfig()->debug,
                        'cache'         => $this->getConfig()->cache,
                        'cache_path'    => $this->getConfig()->cache_path,
                        'cache_refresh' => $this->getConfig()->cache_refresh
                    ));

                    $this->__engines[$type] = $identifier;
                }
            }
        }

        return $result;
    }

    /**
     * Unregister an engine
     *
     * @param string $identifier A engine object identifier string or file type
     * @throws \UnexpectedValueException
     * @return bool Returns TRUE on success, FALSE on failure.
     */
    public function unregisterEngine($identifier)
    {
        $result = false;

        if(strpos($identifier, '.') !== false )
        {
            $identifier = $this->getIdentifier($identifier);
            $class      = $this->getObject('manager')->getClass($identifier);

            if(!$class || !array_key_exists(__NAMESPACE__.'\TemplateEngineInterface', class_implements($class)))
            {
                throw new \UnexpectedValueException(
                    'Engine: '.$identifier.' does not implement TemplateEngineInterface'
                );
            }

            $types = $class::getFileTypes();

        }
        else $types = (array) $identifier;

        if (!empty($types))
        {
            foreach($types as $type)
            {
                if($this->isRegistered($type)) {
                    unset($this->__engines[$type]);
                }
            }
        }

        return $result;
    }

    /**
     * Get a registered engine identifier
     *
     * @param string $type The file type
     * @return string|false The engine identifier
     */
    public function getEngine($type)
    {
        $engine = false;

        if(isset($this->__engines[$type])) {
            $engine = $this->__engines[$type];
        }

        return $engine;
    }

    /**
     * Get a list of all the registered file types
     *
     * @return array
     */
    public function getFileTypes()
    {
        $result = array();
        if(is_array($this->__engines)) {
            $result = array_keys($this->__engines);
        }

        return $result;
    }

    /**
     * Check if the engine is registered
     *
     * @param string $identifier A engine object identifier string or a file type
     * @throws \UnexpectedValueException
     * @return bool TRUE if the engine is a registered, FALSE otherwise.
     */
    public function isRegistered($identifier)
    {
        if(strpos($identifier, '.') !== false )
        {
            $identifier = $this->getIdentifier($identifier);
            $class      = $this->getObject('manager')->getClass($identifier);

            if(!$class || !array_key_exists(__NAMESPACE__.'\TemplateEngineInterface', class_implements($class)))
            {
                throw new \UnexpectedValueException(
                    'Engine: '.$identifier.' does not implement TemplateEngineInterface'
                );
            }

            $types = $class::getFileTypes();
        }
        else $types = (array) $identifier;

        $result = in_array($types, $this->getFileTypes());
        return $result;
    }

    /**
     * Enable or disable engine debugging
     *
     * If debug is enabled the engine will throw an exception if caching fails.
     *
     * @param bool $debug True or false.
     * @return TemplateEngineInterface
     */
    public function setDebug($debug)
    {
        $this->_debug = (bool) $debug;
        return $this;
    }

    /**
     * Check if the engine is running in debug mode
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
     * @param bool   $cache True or false.
     * @param string $path  The cache path
     * @param bool   $reload
     * @return TemplateEngineFactory
     */
    public function setCache($cache, $path, $reload = true)
    {
        $this->_cache        = (bool) $cache;
        $this->_cache_path   = $path;
        $this->_caceh_reload = $reload;

        return $this;
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
