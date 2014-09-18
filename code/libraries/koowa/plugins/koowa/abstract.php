<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Plugin
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Plugin\Koowa
 */
abstract class PlgKoowaAbstract extends JPlugin implements PlgKoowaInterface
{
    /**
     * The object identifier
     *
     * @var KObjectIdentifier
     */
    private $__object_identifier;

    /**
     * The object manager
     *
     * @var KObjectManager
     */
    private $__object_manager;

    /**
     * The object configuration.
     *
     * @var KObjectConfig
     */
    private $__object_config;

    /**
     * Constructor.
     *
     * @param   object  $dispatcher Event dispatcher
     * @param   array|  $config     Configuration options
     */
	public function __construct($dispatcher, $config = array())
	{
        $config = new KObjectConfig($config);

        $this->_initialize($config);

        // Set the object config.
        $this->__object_config = $config;

        // Do not call the parent constructor override it and implement logic ourselves.
        //parent::__construct($dispatcher, $config);

        // Get the parameters.
        if ($config->params)
        {
            if (!$config->params instanceof JRegistry)
            {
                $this->params = new JRegistry;
                $this->params->loadString($config->params);
            }
            else $this->params = $config->params;
        }

        // Get the plugin name.
        $this->_name = $config->name;

        // Get the plugin type.
        $this->_type = $config->type;

        //Inject the identifier
        $this->__object_identifier = KObjectManager::getInstance()->getIdentifier('plg:'.$this->_type.'.'.$this->_name);

        //Inject the object manager
        $this->__object_manager = KObjectManager::getInstance();

        //Connect the plugin to the dispatcher
        if($config->auto_connect) {
            $this->connect($dispatcher);
        }
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_connect' => true,
            'params'       => array()
        ));
    }

    /**
     * Connect the plugin to the dispatcher
     *
     * @param $dispatcher
     */
    public function connect($dispatcher)
    {
        // Self attach the plugin to the joomla event dispatcher
        if($dispatcher instanceof JDispatcher || $dispatcher instanceof JEventDispatcher) {
            $dispatcher->attach($this);
        }
    }

    /**
     * Get the object configuration
     *
     * If no identifier is passed the object config of this object will be returned. Function recursively
     * resolves identifier aliases and returns the aliased identifier.
     *
     * @param  mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return KObjectConfig
     */
    public function getConfig($identifier = null)
    {
        if (isset($identifier)) {
            $result = $this->__object_manager->getIdentifier($identifier)->getConfig();
        } else {
            $result = $this->__object_config;
        }

        return $result;
    }

    /**
     * Get an instance of an object identifier
     *
     * @param KObjectIdentifier|string $identifier An ObjectIdentifier or valid identifier string
     * @param array                    $config     An optional associative array of configuration settings.
     * @return KObjectInterface  Return object on success, throws exception on failure.
     */
    final public function getObject($identifier, array $config = array())
    {
        $result = $this->__object_manager->getObject($identifier, $config);
        return $result;
    }

    /**
     * Gets the service identifier.
     *
     * If no identifier is passed the object identifier of this object will be returned. Function recursively
     * resolves identifier aliases and returns the aliased identifier.
     *
     * @param   string|object    $identifier The class identifier or identifier object
     * @return  KObjectIdentifier
     */
    final public function getIdentifier($identifier = null)
    {
        if (isset($identifier)) {
            $result = $this->__object_manager->getIdentifier($identifier);
        } else {
            $result = $this->__object_identifier;
        }

        return $result;
    }
}
