<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Markdown Template Engine
 *
 * @link https://github.com/erusev/parsedown
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Template\Engine
 */
class KTemplateEngineMarkdown extends KTemplateEngineAbstract
{
    /**
     * Markdown compiler
     *
     * @var callable
     */
    protected $_compiler;

    /**
     * The engine file types
     *
     * @var string
     */
    protected static $_file_types = array('md', 'markdown');

    /**
     * Constructor
     *
     * @param KObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the markdown compiler
        if($config->compiler) {
            $this->setCompiler($config->compiler);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'compiler' => null,
        ));

        parent::_initialize($config);
    }

    /**
     * Load a template by url
     *
     * @param   string  $url The template url
     * @throws InvalidArgumentException If the template could not be located
     * @throws RuntimeException         If the template could not be loaded
     * @throws RuntimeException         If the template could not be compiled
     * @return KTemplateEngineMarkdown
     */
    public function loadFile($url)
    {
        if(!$this->_source)
        {
            $locator = $this->getObject('template.locator.factory')->createLocator($url);

            //Locate the template
            if (!$file = $locator->locate($url)) {
                throw new InvalidArgumentException(sprintf('The template "%s" cannot be located.', $url));
            }

            if(!$cache_file = $this->isCached($file))
            {
                //Load the template
                if(!$source = file_get_contents($file)) {
                    throw new RuntimeException(sprintf('The template "%s" cannot be loaded.', $file));
                }

                //Compile the template
                if(!$source = $this->_compile($source)) {
                    throw new RuntimeException(sprintf('The template "%s" cannot be compiled.', $file));
                }

                $this->cache($file, $source);
                $this->_source = $source;
            }
            else  $this->_source = include $cache_file;
        }

        return $this;
    }

    /**
     * Load the template from a string
     *
     * @param  string  $source  The template source
     * @throws RuntimeException If the template could not be compiled
     * @return KTemplateEngineMarkdown
     */
    public function loadString($source)
    {
        $file = crc32($source);

        if(!$this->_source = $this->isCached($file))
        {
            //Compile the template
            if(!$source = $this->_compile($source)) {
                throw new RuntimeException(sprintf('The template content cannot be compiled.'));
            }

            $this->cache($file, $source);
            $this->_source = $source;
        }

        return $this;
    }

    /**
     * Get callback for compiling markdown
     *
     * @return callable
     */
    public function getCompiler()
    {
        return $this->_compiler;
    }

    /**
     * Set callback for compiling markdown
     *
     * @param  callable $compiler the compiler to set
     * @return KTemplateEngineMarkdown
     */
    public function setCompiler(callable $compiler)
    {
        $this->_compiler = $compiler;
        return $this;
    }

    /**
     * Compile the template
     *
     * @param   string  $source The template source to compile
     * @return string|false The compiled template content or FALSE on failure.
     */
    protected function _compile($source)
    {
        $result = false;
        if(is_callable($this->_compiler)) {
            $result = call_user_func($this->_compiler, $source);
        }

        return $result;
    }
}
