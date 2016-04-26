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
 * Mustache Template Engine
 *
 * @link https://github.com/bobthecow/mustache.php
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Engine\Mustache
 */
class TemplateEngineMustache extends TemplateEngineAbstract implements \Mustache_Loader
{
    /**
     * The engine file types
     *
     * @var string
     */
    protected static $_file_types = array('mustache');

    /**
     * The mustache engine
     *
     * @var \Mustache_Engine
     */
    protected $_mustache;

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

        $this->_mustache = new \Mustache_Engine(array(
            'loader' => $this,
            'cache'  => $this->_cache ? $this->_cache_path : null,
            'strict_callables' => $this->getConfig()->strict_callables,
            'pragmas'          => $this->getConfig()->pragmas,
            'helpers'          => $this->getFunctions()
        ));
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
            'strict_callables' => false,
            'pragmas'          => array(\Mustache_Engine::PRAGMA_FILTERS),
        ));

        parent::_initialize($config);
    }

    /**
     * Render a template
     *
     * @param   string  $source   The template path or content
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @throws \RuntimeException If the template could not be loaded
     * @return string The rendered template
     */
    public function render($source, array $data = array())
    {
        parent::render($source, $data);

        //Let mustache load the template by proxiing through the load() method.
        $result = $this->_mustache->render($source, $data);

        //Render the debug information
        return $this->renderDebug($result);
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * Required by Mustache_Loader Interface. Do not call directly.
     *
     * @param  string $name string The name of the template to load
     * @throws \Mustache_Exception_UnknownTemplateException If a template file is not found.
     * @return string The template source code
     */
    public function load($name)
    {
        try
        {
            $file   = $this->locateSource($name);
            $source = $this->loadSource($file);
        }
        catch (\Exception $e)
        {
            throw new \Mustache_Exception_UnknownTemplateException(
                sprintf('The template "%s" cannot be loaded.', $file)
            );
        }

        return $source;
    }
}