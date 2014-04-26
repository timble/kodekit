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
     * The object identifiers
     *
     * @var array
     */
    protected $_identifiers;

    /**
     * The class namespaces
     *
     * @var array
     */
    protected $_namespaces;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_aliases     = $config->aliases;
        $this->_identifiers = $config->identifiers;
        $this->_namespaces  = $config->namespaces;
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
            'aliases'     => array(),
            'identifiers' => array(),
            'namespaces'  => array(),
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
        $loader  = $this->getClassLoader();

        //Identifiers
        foreach ($this->_identifiers as $identifier => $config) {
            $manager->setIdentifier($identifier, $config, false);
        }

        //Aliases
        foreach ($this->_aliases as $alias => $identifier) {
            $manager->registerAlias($identifier, $alias);
        }

        //Namespaces
        foreach ($this->_namespaces as $type => $namespaces)
        {
            if($locator = $loader->getLocator($type)) {
                $locator->registerNamespaces($namespaces);
            }
        }
    }
}