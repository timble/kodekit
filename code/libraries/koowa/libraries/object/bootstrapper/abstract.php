<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Object Bootstrapper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Bootstrapper
 */
abstract class KObjectBootstrapperAbstract extends KObject implements KObjectBootstrapperInterface
{
    /**
     * The object manager
     *
     * @var KObjectManagerInterface
     */
    private $__object_manager;

    /**
     * The bootstrapper priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->__object_manager = $config->object_manager;
        $this->_priority        = $config->priority;
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
            'priority'    => self::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the object manager
     *
     * @return KObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->__object_manager;
    }

    /**
     * Get the class loader
     *
     * @return KClassLoaderInterface
     */
    public function getClassLoader()
    {
        return $this->getObjectManager()->getClassLoader();
    }

    /**
     * Get the priority of the bootstrapper
     *
     * @return  integer The priority level
     */
    public function getPriority()
    {
        return $this->_priority;
    }
}