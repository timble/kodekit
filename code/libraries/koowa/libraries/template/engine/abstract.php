<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Template Engine
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Template\Engine
 */
abstract class KTemplateEngineAbstract extends KTemplateAbstract implements KTemplateEngineInterface
{
    /**
     * The engine file types
     *
     * @var string
     */
    protected static $_file_types = array();

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     *
     * @param KObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Reset the stack
        $this->_stack = array();

        //Set the functions
        $this->_functions = KObjectConfig::unbox($config->functions);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional KObjectConfig object with configuration options
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'functions' => array(
                'object'    => array($this, 'getObject'),
                'translate' => array($this->getObject('translator'), 'translate'),
                'json'      => 'json_encode',
                'format'    => 'sprintf',
                'replace'   => 'strtr',
            ),
        ));

        parent::_initialize($config);
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
}