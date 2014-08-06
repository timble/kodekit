<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
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
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->__object_manager = $config->object_manager;
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
     * Check if the bootstrapper has been run
     *
     * @return bool TRUE if the bootstrapping has run FALSE otherwise
     */
    public function isBootstrapped()
    {
        return false;
    }
}