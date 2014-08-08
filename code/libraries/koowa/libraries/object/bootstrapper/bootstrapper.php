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
class KObjectBootstrapper extends KObjectBootstrapperAbstract implements KObjectSingleton
{
    /**
     * List of bootstrapped directories
     *
     * @var array
     */
    protected $_directories;

    /**
     * List of bootstrapped components
     *
     * @var array
     */
    protected $_components;

    /**
     * List of config files
     *
     * @var array
     */
    protected $_files;

    /**
     * List of identifier aliases
     *
     * @var array
     */
    protected $_aliases;

    /**
     * Bootstrapped status.
     *
     * @var bool
     */
    protected $_bootstrapped;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_bootstrapped = false;

        //Force a reload if cache is enabled and we have already bootstrapped
        if($config->force_reload && $config->bootstrapped)
        {
            $config->bootstrapped = false;
            $config->directories = array();
            $config->files       = array();
            $config->components  = array();
            $config->aliases     = array();
            $config->identifiers = array();
        }

        $this->_directories  = KObjectConfig::unbox($config->directories);
        $this->_components   = KObjectConfig::unbox($config->components);
        $this->_files        = KObjectConfig::unbox($config->files);
        $this->_aliases      = KObjectConfig::unbox($config->aliases);
        $this->_identifiers  = KObjectConfig::unbox($config->identifiers);
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
            'force_reload' => false,
            'bootstrapped' => false,
            'directories'  => array(),
            'files'        => array(),
            'components'   => array(),
            'aliases'      => array(),
            'identifiers'  => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Bootstrap
     *
     * The bootstrap cycle can be run only once
     *
     * @return void
     */
    public function bootstrap()
    {
        $identifiers = $this->_identifiers;
        $aliases     = $this->_aliases;

        if(!$this->isBootstrapped())
        {
            foreach($this->_components as $identifier => $component)
            {
                $name   = $component['name'];
                $path   = $component['path'];
                $vendor = $component['vendor'];

                /*
                 * Setup the component class and object locators
                 *
                 * Locators are always setup as the  cannot be cached in the registry objects.
                 */
                if($vendor)
                {
                    //Register class namespace
                    $namespace = ucfirst($name);
                    $this->getClassLoader()->getLocator('component')->registerNamespace($namespace, $path);

                    //Register object manager package
                    $this->getObjectManager()->getLocator('com')->registerPackage($name, $vendor);
                }
            }

            /*
             * Load resources
             *
             * If cache is enabled and the bootstrapper has been run we do not reload the config resources
             */
            if(!$this->getConfig()->bootstrapped)
            {
                $factory = $this->getObject('object.config.factory');

                foreach($this->_files as $filename)
                {
                    $array = $factory->fromFile($filename, false);

                    if(isset($array['priority'])) {
                        $priority = $array['priority'];
                    } else {
                        $priority = self::PRIORITY_NORMAL;
                    }

                    if(isset($array['aliases']))
                    {
                        if(!isset($aliases[$priority])) {
                            $aliases[$priority] = array();
                        }

                        $aliases[$priority] = array_merge($aliases[$priority], $array['aliases']);;
                    }

                    if(isset($array['identifiers']))
                    {
                        if(!isset($identifiers[$priority])) {
                            $identifiers[$priority] = array();
                        }

                        $identifiers[$priority] = array_merge_recursive($identifiers[$priority], $array['identifiers']);;
                    }
                }

                /*
                * Set the identifiers
                *
                * Collect identifiers by priority and then flatten the array.
                */
                $identfiers_flat = array();

                foreach ($identifiers as $priority => $merges) {
                    $identfiers_flat = array_merge_recursive($merges, $identfiers_flat);
                }

                foreach ($identfiers_flat as $identifier => $config) {
                    $this->getObjectManager()->setIdentifier(new KObjectIdentifier($identifier, $config));
                }

                /*
                * Set the aliases
                *
                * Collect aliases by priority and then flatten the array.
                */
                $aliases_flat = array();

                foreach ($aliases as $priority => $merges) {
                    $aliases_flat = array_merge($merges, $aliases_flat);
                }

                foreach($aliases_flat as $alias => $identifier) {
                    $this->getObjectManager()->registerAlias($identifier, $alias);
                }

                /*
                * Set the bootstrapper config.
                *
                * If cache is enabled this will prevent the bootstrapper from reloading the config resources
                */
                $this->getObjectManager()->setIdentifier(new KObjectIdentifier('lib:object.bootstrapper', array(
                    'bootstrapped' => true,
                    'directories'  => $this->_directories,
                    'components'   => $this->_components,
                    'files'        => $this->_files,
                    'aliases'      => $aliases_flat,
                )));
            }
            else
            {
                foreach($aliases as $alias => $identifier) {
                    $this->getObjectManager()->registerAlias($identifier, $alias);
                }
            }

            $this->_bootstrapped = true;
        }
    }

    /**
     * Register a component to be bootstrapped.
     *
     * If the component contains a /resources/config/bootstrapper.php file it will be registered. Class and object
     * locators will be setup for vendor only components.
     *
     * @param string $name      The component name
     * @param string $path      The component path
     * @param string $vendor    The vendor name. Vendor is optional and can be NULL
     * @return KObjectBootstrapper
     */
    public function registerComponent($name, $path, $vendor = null)
    {
        //Get the bootstrapper identifier
        if($vendor) {
            $identifier = 'com://'.$vendor.'/'.$name.'.object.bootstrapper.component';
        } else {
            $identifier = 'com:'.$name.'.object.bootstrapper.component';
        }

        if(!isset($this->_components[$identifier]))
        {
            $this->_components[$identifier] = array(
                'name'   => $name,
                'path'   => $path,
                'vendor' => $vendor
            );

            //Register the config file
            $this->registerFile($path .'/resources/config/bootstrapper.php');
        }

        return $this;
    }

    /**
     * Register components from a directory to be bootstrapped
     *
     * All the first level directories are assumed to be component folders and will be registered.
     *
     * @param string  $directory
     * @param string  $vendor
     * @return KObjectBootstrapper
     */
    public function registerDirectory($directory, $vendor = null)
    {
        if(!isset($this->_directories[$directory]))
        {
            foreach (new DirectoryIterator($directory) as $dir)
            {
                //Only get the component directory names
                if ($dir->isDot() || !$dir->isDir() || !preg_match('/^[a-zA-Z]+/', $dir->getBasename())) {
                    continue;
                }

                //Get the component path
                $path = $dir->getPathname();

                //Get the component name (strip prefix if it exists)
                $parts = explode('_', (string) $dir);

                if(count($parts) > 1) {
                    $name = $parts[1];
                } else {
                    $name = $parts[0];
                }

                $this->registerComponent($name, $path, $vendor);
            }

            $this->_directories[$directory] = true;
        }

        return $this;
    }

    /**
     * Register a configuration file to be bootstrapped
     *
     * @param string $filename The absolute path to the file
     * @return KObjectBootstrapper
     */
    public function registerFile($filename)
    {
        if(file_exists($filename)) {
            $this->_files[$filename] = $filename;
        }

        return $this;
    }

    /**
     * Check if the bootstrapper has been run
     *
     * @return bool TRUE if the bootstrapping has run FALSE otherwise
     */
    public function isBootstrapped()
    {
        return $this->_bootstrapped;
    }
}
