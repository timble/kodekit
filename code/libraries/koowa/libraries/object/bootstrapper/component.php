<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Object Bootstrapper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Bootstrapper
 */
class KObjectBootstrapperComponent extends KObjectBootstrapperAbstract
{
    /**
     * The object aliases
     *
     * @var array
     */
    protected $_aliases;

    /**
     * The object mixins
     *
     * @var array
     */
    protected $_mixins;

    /**
     * The object decorators
     *
     * @var array
     */
    protected $_decorators;

    /**
     * The object configs
     *
     * @var array
     */
    protected $_configs;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_aliases    = $config->aliases;
        $this->_mixins     = $config->mixins;
        $this->_decorators = $config->decorators;
        $this->_configs    = $config->configs;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'aliases'    => array(),
            'configs'    => array(),
            'mixins'     => array(),
            'decorators' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Bootstrap the object manager
     *
     * @return void
     */
    public function bootstrap()
    {
        $manager = $this->getObjectManager();

        //Aliases
        foreach ($this->_aliases as $alias => $identifier) {
            $manager->registerAlias($alias, $identifier);
        }

        //Configs
        foreach ($this->_configs as $identifier => $config) {
            $manager->setConfig($identifier, $config);
        }

        //Mixins
        foreach ($this->_mixins as $identifier => $mixins)
        {
            foreach($mixins as $key => $value)
            {
                if (is_numeric($key)) {
                    $manager->registerMixin($identifier, $value);
                } else {
                    $manager->registerMixin($identifier, $key, $value);
                }
            }
        }

        //Decorators
        foreach ($this->_decorators as $identifier => $decorators)
        {
            foreach($decorators as $key => $value)
            {
                if (is_numeric($key)) {
                    $manager->registerDecorator($identifier, $value);
                } else {
                    $manager->registerDecorator($identifier, $key, $value);
                }
            }
        }
    }
}