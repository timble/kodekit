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
     * List of bootstrapped components
     *
     * @var array
     */
    private $__bootstrapped;

    /**
     * List of bootstrappers
     *
     * @var array
     */
    protected $_bootstrappers = array();

    /**
     * Bootstrap
     *
     * The bootstrap cycle can only be run once. Subsequent bootstrap calls will not re-run the cycle.
     *
     * @return void
     */
    final public function bootstrap()
    {
        $chain = $this->getObject('lib:object.bootstrapper.chain');

        foreach($this->_bootstrappers as $bootstrapper => $config)
        {
            if(!isset($this->__bootstrapped[$bootstrapper]))
            {
                $instance = $this->getObject($bootstrapper, $config);
                $chain->addBootstrapper($instance);

                $this->__bootstrapped[$bootstrapper] = true;
            }
        }

        $chain->bootstrap();

        //Clear bootstrappers list
        $this->_bootstrappers = array();
    }

    /**
     * Register a component
     *
     * This method will setup the class and object locators for the component and register the bootstrapper if one can
     * be found.
     *
     * @param string $name      The component name
     * @param string $vendor    The vendor name
     * @param string $path      The component path
     * @return KObjectBootstrapper
     */
    public function registerComponent($name, $vendor = null, $path = null)
    {
        //Setup the component class and object locators
        if($vendor)
        {
            //Register class namespace
            $namespace = ucfirst($name);
            $this->getClassLoader()->getLocator('component')->registerNamespace($namespace, $path);

            //Register object manager package
            $this->getObjectManager()->getLocator('com')->registerPackage($name, $vendor);
        }

        //Get the bootstrapper identifier
        if($vendor) {
            $identifier = 'com://'.$vendor.'/'.$name.'.object.bootstrapper.component';
        } else {
            $identifier = 'com:'.$name.'.object.bootstrapper.component';
        }

        //Register the component bootstrapper
        if(!isset($this->_bootstrappers[$identifier]) && $path)
        {
            $config = $path .'/components/com_'.$name.'/resources/config/bootstrapper.php';

            if(file_exists($config)) {
                $this->_bootstrappers[$identifier] = include $config;
            }
        }

        return $this;
    }

    /**
     * Register components from a directory
     *
     * @param string  $directory
     * @param string  $domain
     * @return KObjectBootstrapper
     */
    public function registerDirectory($directory, $domain = null)
    {
        foreach (new DirectoryIterator($directory) as $dir)
        {
            //Only get the component directory names
            if ($dir->isDot() || !$dir->isDir() || !preg_match('/^[a-zA-Z]+/', $dir->getBasename())) {
                continue;
            }

            $path = dirname(dirname($dir->getPathname()));
            $name = substr($dir, 4);

            $this->registerComponent((string) $name, $domain, $path);
        }

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
