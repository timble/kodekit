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
 * Abstract Template Engine
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Engine
 */
abstract class TemplateEngineAbstract extends TemplateAbstract implements TemplateEngineInterface
{
    /**
     * The engine file types
     *
     * @var array
     */
    protected static $_file_types = array();

    /**
     * Template stack
     *
     * Used to track recursive template loading
     *
     * @var array
     */
    private $__stack;

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
     * Constructor
     *
     * @param ObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Reset the stack
        $this->__stack = array();

        //Set debug
        $this->setDebug($config->debug);

        //Set cache
        $this->setCache($config->cache, $config->cache_path, $config->cache_reload);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'debug'        => \Kodekit::getInstance()->isDebug(),
            'cache'        => \Kodekit::getInstance()->isCache(),
            'cache_path'   => '',
            'functions'    => array(
                'object'    => array($this, 'getObject'),
                'translate' => array($this->getObject('translator'), 'translate'),
                'json'      => 'json_encode',
                'format'    => 'sprintf',
                'replace'   => 'strtr',
            )
        ))->append(array(
            'cache_reload' => $config->debug,
        ));

        parent::_initialize($config);
    }

    /**
     * Render a template
     *
     * @param   string  $source    The template url or content
     * @param   array   $data       An associative array of data to be extracted in local template scope
     * @throws \RuntimeException If the template could not be loaded
     * @return string The rendered template source
     */
    public function render($source, array $data = array())
    {
        parent::render($source, $data);

        //Push the template on the stack
        array_push($this->__stack, $source);

        return $source;
    }

    /**
     * Render a partial template
     *
     * This method merges the data passed in with the data from the parent template. If the partial template
     * has different file type the method will try to allocate it by jumping out of the local template scope.
     *
     * @param   string  $url      The template url
     * @param   array   $data     The data to pass to the template
     * @throws \RuntimeException  If a partial template url could not be fully qualified
     * @return  string The rendered template content
     */
    public function renderPartial($url, array $data = array())
    {
        //Qualify relative template url
        if(!parse_url($url, PHP_URL_SCHEME))
        {
            if(!$template = end($this->__stack)) {
                throw new \RuntimeException('Cannot qualify partial template url');
            }

            $basepath = dirname($template);

            //Resolve relative path
            if($path = trim('.', dirname($url)))
            {
                $count = 0;
                $total = count(explode('/', $path));

                while ($count++ < $total) {
                    $basepath = dirname($basepath);
                }

                $basename = $url;
            }
            else $basename = basename($url);

            $url = $basepath. '/' .$basename;
        }

        if(array_search($url, $this->__stack))
        {
            throw new \RuntimeException(sprintf(
                'Template recursion detected while importing "%s" in "%s"', $url
            ));
        }

        $type = pathinfo( $this->locateSource($url), PATHINFO_EXTENSION);
        $data = array_merge((array) $this->getData(), $data);

        //If the partial requires a different engine create it and delegate
        if(!in_array($type, $this->getFileTypes()))
        {
            $result = $this->getObject('template.engine.factory')
                ->createEngine($type, array('functions' => $this->getFunctions()))
                ->render($url, $data);
        }
        else $result = $this->render($url, $data);

        //Remove the template from the stack
        array_pop($this->__stack);

        return $result;
    }

    /**
     * Render debug information
     *
     * @param  string  $source  The template source
     * @return string The rendered template source
     */
    public function renderDebug($source)
    {
        $template = end($this->__stack);

        if($this->getObject('filter.path')->validate($template)) {
            $path = $this->locateSource($template);
        } else {
            $path = '';
        }

        //Render debug comments
        if($this->isDebug())
        {
            $type  = $this->getIdentifier()->getName();
            $path = str_replace(rtrim(\Kodekit::getInstance()->getRootPath(), '/').'/', '', $path);

            $format  = PHP_EOL.'<!--BEGIN '.$type.':render '.$path.' -->'.PHP_EOL;
            $format .= '%s';
            $format .= PHP_EOL.'<!--END '.$type.':render '.$path.' -->'.PHP_EOL;

            $source = sprintf($format, trim($source));
        }

        return $source;
    }

    /**
     * Locate a template file, given it's url.
     *
     * @param   string  $url The template url
     * @throws \InvalidArgumentException If the template could not be located
     * @throws \RuntimeException If a partial template url could not be fully qualified
     * @return string   The template real path
     */
    public function locateSource($url)
    {
        if (!$file = $this->getObject('template.locator.factory')->locate($url)) {
            throw new \InvalidArgumentException(sprintf('The template "%s" cannot be located.', $url));
        }

        return $file;
    }

    /**
     * Gets the source code of a template, given its url
     *
     * @param  string $path        The path of the template to load
     * @throws \RuntimeException   If the template could not be loaded
     * @return string The template source code
     */
    public function loadSource($path)
    {
        if(!$source = file_get_contents($path)) {
            throw new \RuntimeException(sprintf('The template "%s" cannot be loaded.', $path));
        }

        return $source;
    }

    /**
     * Cache the template source to a file
     *
     * Write the template source to a file cache. Requires cache to be enabled. This method will throw exceptions if
     * caching fails and debug is enabled. If debug is disabled FALSE will be returned.
     *
     * @param  string $name     The file name
     * @param  string $source   The template source
     * @throws \RuntimeException If the file path does not exist
     * @throws \RuntimeException If the file path is not writable
     * @throws \RuntimeException If template cannot be written to the cache
     * @return string|false The cached file path. FALSE if the file cannot be stored in the cache
     */
    public function cacheSource($name, $source)
    {
        if($this->_cache)
        {
            $path = $this->_cache_path;

            if(!is_dir($path) && (false === @mkdir($path, 0755, true) && !is_dir($path)))
            {
                if($this->isDebug()) {
                    throw new \RuntimeException(sprintf('The template cache path "%s" does not exist', $path));
                } else {
                    return false;
                }
            }

            if(!is_writable($path))
            {
                if($this->isDebug()) {
                    throw new \RuntimeException(sprintf('The template cache path "%s" is not writable', $path));
                } else {
                    return false;
                }
            }

            $hash = crc32($name.PHP_VERSION);
            $file = $path.'/template_'.$hash.'.php';

            if(@file_put_contents($file, $source) === false)
            {
                if($this->isDebug()) {
                    throw new \RuntimeException(sprintf('The template cannot be cached in "%s"', $file));
                } else {
                    return false;
                }
            }

            //Override default permissions for cache files
            @chmod($file, 0666 & ~umask());

            return $file;
        }

        return false;
    }

    /**
     * Get the engine supported file types
     *
     * @return array
     */
    public static function getFileTypes()
    {
        return static::$_file_types;
    }

    /**
     * Enable or disable debug
     *
     * @param bool $debug True or false.
     * @return TemplateEngineAbstract
     */
    public function setDebug($debug)
    {
        $this->_debug = (bool) $debug;
        return $this;
    }

    /**
     * Check if the template engine is running in debug mode
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
     * @return TemplateEngineAbstract
     */
    public function setCache($cache, $path, $reload = true)
    {
        $this->_cache        = (bool) $cache;
        $this->_cache_path   = $path;
        $this->_cache_reload = $reload;

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

    /**
     * Check if a file exists in the cache
     *
     * @param string $file The file name
     * @return string|false The cache file path. FALSE if the file cannot be found in the cache
     */
    public function isCached($file)
    {
        $result = false;

        if($this->_cache)
        {
            $hash   = crc32($file.PHP_VERSION);
            $cache  = $this->_cache_path.'/template_'.$hash.'.php';
            $result = is_file($cache) ? $cache : false;

            if($result && $this->_cache_reload && is_file($file))
            {
                if(filemtime($cache) < filemtime($file)) {
                    $result = false;
                }
            }
        }

        return $result;
    }
}