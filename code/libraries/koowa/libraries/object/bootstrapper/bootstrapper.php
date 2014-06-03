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
class KObjectBootstrapper extends KObjectBootstrapperAbstract implements KObjectSingleton
{
    /**
     * Bootstrapped status
     *
     * @var boolean
     */
    private $__bootstrapped;

    /**
     * List of bootstrappers
     *
     * @var array
     */
    protected $_bootstrappers;

    /**
     * Bootstrap
     *
     * The bootstrap cycle can only be run once. Subsequent bootstrap calls will not re-run the cycle.
     *
     * @return void
     */
    final public function bootstrap()
    {
        if(!$this->__bootstrapped)
        {
            $chain = $this->getObject('lib:object.bootstrapper.chain');

            foreach($this->_bootstrappers as $bootstrapper)
            {
                list($identifier, $config) = $bootstrapper;

                $instance = $this->getObject($identifier, $config);
                $chain->addBootstrapper($instance);
            }

            $chain->bootstrap();

            $this->__bootstrapped == true;
        }
    }

    /**
     * Register a bootstrapper
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return KObjectBootstrapper
     */
    public function registerBootstrapper($bootstrapper, $config = array())
    {
        $this->_bootstrappers[] = array($bootstrapper, $config);
        return $this;
    }

    /**
     * Prevent recursive bootstrapping
     *
     * @return null|string
     */
    final public function getHandle()
    {
        return null;
    }
}
