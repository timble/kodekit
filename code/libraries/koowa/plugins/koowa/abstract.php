<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
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
     * Constructor.
     *
     * @param   object  $dispatcher Event dispatcher
     * @param   array|  $config     Configuration options
     */
	public function __construct($dispatcher, $config = array())
	{
        // Do not call the parent constructor override it and implement logic ourselves.
        //parent::__construct($dispatcher, $config);

        // Get the parameters.
        if (isset($config['params']))
        {
            if (!$config['params'] instanceof JRegistry)
            {
                $this->params = new JRegistry;
                $this->params->loadString($config['params']);
            }
            else $this->params = $config['params'];
        }

        // Get the plugin name.
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }

        // Get the plugin type.
        if (isset($config['type'])) {
            $this->_type = $config['type'];
        }

        //Inject the identifier
        $this->__object_identifier = KObjectManager::getInstance()->getIdentifier('plg:'.$this->_type.'.'.$this->_name);

        //Inject the object manager
        $this->__object_manager = KObjectManager::getInstance();

        //Connect the plugin to the dispatcher
        $this->connect($dispatcher);
	}

    /**
     * Get an instance of an object identifier
     *
     * @param KObjectIdentifier|string $identifier An ObjectIdentifier or valid identifier string
     * @param array  			      $config     An optional associative array of configuration settings.
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

    /**
     * Connect the plugin to the dispatcher
     *
     * @param $dispatcher
     */
    public function connect($dispatcher)
    {
        //Self attach the plugin to the joomla event dispatcher
        if($dispatcher instanceof JDispatcher) {
            $dispatcher->attach($this);
        }
    }
}
