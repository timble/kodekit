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
 * Abstract Filesystem Locator
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Filesystem\Locator
 */
abstract class FilesystemLocatorAbstract extends ObjectAbstract implements FilesystemLocatorInterface, ObjectMultiton
{
    /**
     * The stream name
     *
     * @var string
     */
    protected static $_name = '';

    /**
     * The path templates
     *
     * @var array
     */
    private $__path_templates = array();

    /**
     * Found locations map
     *
     * @var array
     */
    private $__location_cache;

    /**
     * Constructor
     *
     * @param ObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Register the path templates
        foreach($config->path_templates as $template) {
            $this->registerPathTemplate($template);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'path_templates' => array()
        ));

        parent::_initialize($config);
    }

    /**
     * Get the locator name
     *
     * @return string The stream name
     */
    public static function getName()
    {
        return static::$_name;
    }

    /**
     * Locate the resource based on a url
     *
     * @param  string $url  The resource url
     * @return string|false  The physical file path for the resource or FALSE if the url cannot be located
     */
    final public function locate($url)
    {
        $result = false;

        if(!isset($this->__location_cache[$url]))
        {
            $info = $this->parseUrl($url);

            //Find the file
            foreach($this->getPathTemplates($url) as $template)
            {
                $path = str_replace(
                    array('<Package>'     , '<Path>'     ,'<File>'      , '<Format>'     , '<Type>'),
                    array($info['package'], $info['path'], $info['file'], $info['format'], $info['type']),
                    $template
                );

                if ($results = glob($path))
                {
                    foreach($results as $file)
                    {
                        if($result = $this->realPath($file)) {
                            break (2);
                        }
                    }
                }
            }

            $this->__location_cache[$url] = $result;
        }

        return $this->__location_cache[$url];
    }

    /**
     * Parse the url
     *
     * @param  string $url The language url
     * @return array
     */
    public function parseUrl($url)
    {
        $scheme   = parse_url($url, PHP_URL_SCHEME);
        $domain   = parse_url($url, PHP_URL_HOST);
        $basename = pathinfo($url, PATHINFO_BASENAME);

        $parts = explode('.', $basename);

        if(count($parts) == 3)
        {
            $type  =  array_pop($parts);
            $format = array_pop($parts);
            $file   = array_pop($parts);
        }
        else
        {
            $type   = '*';
            $format = array_pop($parts);
            $file   = array_pop($parts);
        }

        $path = str_replace(array($scheme.'://', $scheme.':'), '', dirname($url));

        if (strpos($path, $domain.'/') === 0) {
            $path = substr($path, strlen($domain)+1);
        }

        $parts = explode('/', $path);

        $info = array(
            'type'    => $type,
            'domain'  => $domain,
            'package' => array_shift($parts),
            'path'    => implode('/', $parts),
            'file'    => $file,
            'format'  => $format ?: '*',
        );

        return $info;
    }

    /**
     * Register a path template
     *
     * @param  string $template   The path template
     * @param  bool $prepend      If true, the template will be prepended instead of appended.
     * @return FilesystemLocatorAbstract
     */
    public function registerPathTemplate($template, $prepend = false)
    {
        if($prepend) {
            array_unshift($this->__path_templates, $template);
        } else {
            array_push($this->__path_templates, $template);
        }

        return $this;
    }

    /**
     * Get the list of path templates
     *
     * @param  string $url   The Template url
     * @return array The path templates
     */
    public function getPathTemplates($url)
    {
        return $this->__path_templates;
    }

    /**
     * Get a path from an file
     *
     * Function will check if the path is an alias and return the real file path
     *
     * @param  string $file The file path
     * @return string The real file path
     */
    final public function realPath($file)
    {
        $result = false;
        $path   = dirname($file);

        // Is the path based on a stream?
        if (strpos($path, '://') === false)
        {
            // Not a stream, so do a realpath() to avoid directory traversal attempts on the local file system.
            $path = realpath($path); // needed for substr() later
            $file = realpath($file);
        }

        // The substr() check added to make sure that the realpath() results in a directory registered so that
        // non-registered directories are not accessible via directory traversal attempts.
        if (file_exists($file) && substr($file, 0, strlen($path)) == $path) {
            $result = $file;
        }

        return $result;
    }

    /**
     * Returns true if the resource is still fresh.
     *
     * @param  string $url    The resource url
     * @param int     $time   The last modification time of the resource (timestamp)
     * @return bool TRUE if the resource is still fresh, FALSE otherwise
     */
    public function isFresh($url, $time)
    {
        if($file = $this->locate($url)) {
            return filemtime($file) < $time;
        }

        return false;
    }
}
