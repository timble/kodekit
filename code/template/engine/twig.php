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
 * Twig Template Engine
 *
 *  @link https://github.com/fabpot/Twig
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Engine
 */
class TemplateEngineTwig extends TemplateEngineAbstract
{
    /**
     * The engine file types
     *
     * @var string
     */
    protected static $_file_types = array('twig');

    /**
     * The twig environment
     *
     * @var Twig_Environment
     */
    protected $_twig;

    /**
     * Constructor
     *
     * @param ObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Reset the stack
        $this->_stack = array();

        $this->_twig = new \Twig_Environment($this,  array(
            'cache'       => $this->_cache ? $this->_cache_path : false,
            'auto_reload' => $this->_cache_reload,
            'debug'       => $config->debug,
            'autoescape'  => $config->autoescape,
            'strict_variables' => $config->strict_variables,
            'optimizations'    => $config->optimizations,
        ));

        //Register functions in twig
        foreach($this->getFunctions() as $name => $callable)
        {
            $function = new \Twig_SimpleFunction($name, $callable);
            $this->_twig->addFunction($function);
        }
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
        $self = $this;

        $config->append(array(
            'autoescape'       => true,
            'strict_variables' => false,
            'optimizations'    => -1,
            'functions'        => array(
                'import' => function($url, $data) use($self) {
                    return $self->renderPartial($url, $data);
                }
            ),
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

        //Let twig load the content by proxiing through the getSource() method.
        $result = $this->_twig->render($source, $data);

        //Render the debug information
        return $this->renderDebug($result);
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * Required by Twig_LoaderInterface Interface. Do not call directly.
     *
     * @param  string $name        The name of the template to load
     * @throws \RuntimeException   If the template could not be loaded
     * @return string The template source code
     */
    public function getSource($name)
    {
        $file   = $this->locateSource($name);
        $source = $this->loadSource($file);

        return $source;
    }

    /**
     * Unregister a function
     *
     * @param string    $name   The function name
     * @return TemplateEngineTwig
     */
    public function unregisterFunction($name)
    {
        parent::unregisterFunction($name);

        $functions = $this->_twig->getFunctions();

        if(isset($functions[$name])) {
            unset($functions[$name]);
        }

        return $this;
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * Required by Twig_LoaderInterface Interface. Do not call directly.
     *
     * @param  string $name string The name of the template to load
     * @return string The cache key
     */
    public function getCacheKey($name)
    {
        return crc32($name);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * Required by Twig_Loader Interface. Do not call directly.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     */
    public function isFresh($name, $time)
    {
        if(is_file($name)) {
            return filemtime($name) <= $time;
        }

        return true;
    }
}
