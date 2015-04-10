<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Template Cache
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateCache extends KObjectDecorator implements KTemplateInterface
{
    /**
     * The registry cache namespace
     *
     * @var boolean
     */
    protected $_namespace = 'koowa';

    /**
     * Constructor
     *
     * @param KObjectConfig  $config  A ObjectConfig object with optional configuration options
     * @throws RuntimeException  If the APC PHP extension is not enabled or available
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!static::isSupported()) {
            throw new RuntimeException('Unable to use TemplateEngineCache. APC is not enabled.');
        }
    }

    /**
     * Get the template cache namespace
     *
     * @param string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * Get the template cache namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Load a template by url
     *
     * @param   string  $url    The template url
     * @throws \InvalidArgumentException If the template could not be found
     * @return KTemplateInterface
     */
    public function loadFile($url)
    {
        $this->getDelegate()->loadFile($url);
        return $this;
    }

    /**
     * Set the template content from a string
     *
     * @param  string   $content The template content
     * @return KTemplateInterface
     */
    public function loadString($content)
    {
        $this->getDelegate()->loadString($content);
        return $this;
    }

    /**
     * Render the template
     *
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @throws  InvalidArgumentException If the template could not be located
     * @return KTemplateInterface
     */
    public function render(array $data = array())
    {
        $this->getDelegate()->render($data);
        return $this;
    }

    /**
     * Get a template property
     *
     * @param   string  $property The property name.
     * @param   mixed   $default  Default value to return.
     * @return  string  The property value.
     */
    public function get($property, $default = null)
    {
        return $this->getDelegate()->get($property, $default);
    }

    /**
     * Get the template data
     *
     * @return  array   The template data
     */
    public function getData()
    {
        return $this->getDelegate()->getData();
    }

    /**
     * Register a function
     *
     * @param string  $name      The function name
     * @param string  $function  The callable
     * @return KTemplateEngineInterface
     */
    public function registerFunction($name, $function)
    {
        $this->getDelegate()->registerFunction($name, $function);
        return $this;
    }

    /**
     * Unregister a function
     *
     * @param string    $name   The function name
     * @return KTemplateEngineInterface
     */
    public function unregisterFunction($name)
    {
        $this->getDelegate()->unregisterFunction($name);
        return $this;
    }

    /**
     * Checks if the APC PHP extension is enabled
     *
     * @return bool
     */
    public static function isSupported()
    {
        return extension_loaded('apc');
    }

    /**
     * Set the decorated translator
     *
     * @param   KTemplateEngineInterface $delegate The decorated template engine
     * @return  KTemplateCache
     * @throws  InvalidArgumentException If the delegate does not implement the TranslatorInterface
     */
    public function setDelegate($delegate)
    {
        if (!$delegate instanceof KTemplateEngineInterface) {
            throw new InvalidArgumentException('Delegate: '.get_class($delegate).' does not implement TemplateEngineInterface');
        }

        return parent::setDelegate($delegate);
    }

    /**
     * Get the decorated object
     *
     * @return KTemplateCache
     */
    public function getDelegate()
    {
        return parent::getDelegate();
    }
}