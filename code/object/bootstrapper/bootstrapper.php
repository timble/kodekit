<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Object Bootstrapper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object\Bootstrapper
 */
final class ObjectBootstrapper extends Object implements ObjectBootstrapperInterface, ObjectSingleton
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
     * Namespace/path map
     *
     * @var array
     */
    protected $_namespaces;

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
     * Manifests cache
     *
     * @var array
     */
    protected $_manifests;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_bootstrapped = false;

        //Force a reload if cache is enabled and we have already bootstrapped
        if($config->force_reload && $config->bootstrapped)
        {
            $config->bootstrapped   = false;
            $config->directories    = array();
            $config->components     = array();
            $config->namespaces     = array();
            $config->files          = array();
            $config->aliases        = array();
            $config->identifiers    = array();
        }

        $this->_directories  = ObjectConfig::unbox($config->directories);
        $this->_components   = ObjectConfig::unbox($config->components);
        $this->_namespaces   = ObjectConfig::unbox($config->namespaces);
        $this->_files        = ObjectConfig::unbox($config->files);
        $this->_aliases      = ObjectConfig::unbox($config->aliases);
        $this->_identifiers  = ObjectConfig::unbox($config->identifiers);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'force_reload' => false,
            'bootstrapped' => false,
            'directories'  => array(),
            'components'   => array(),
            'namespaces'   => array(),
            'files'        => array(),
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
     * @throws \RuntimeException  If the component has already been registered
     * @throws \RuntimeException  If the parent component cannot be found
     * @return void
     */
    public function bootstrap()
    {
        if(!$this->isBootstrapped())
        {
            $manager = $this->getObject('manager');

            /*
             * Load resources
             *
             * If cache is enabled and the bootstrapper has been run we do not reload the config resources
             */
            if(!$this->getConfig()->bootstrapped)
            {
                $factory = $this->getObject('object.config.factory');

                foreach($this->_manifests as $manifest)
                {
                    if (isset($manifest['identifier']))
                    {
                        $identifier = $manifest['identifier'];

                        if (isset($this->_components[$identifier])) {
                            throw new \RuntimeException(sprintf('Cannot re-bootstrap component: %s', $identifier));
                        }

                        //Set the path
                        $this->_components[$identifier] = $manifest['paths'];

                        //Set the namespace
                        if (isset($manifest['namespace']))
                        {
                            $namespace = $manifest['namespace'];
                            $this->_namespaces[$identifier] = array($namespace => $manifest['paths']);
                        }
                    }
                }

                foreach($this->_manifests as  $manifest)
                {
                    if (isset($manifest['extends']))
                    {
                        $extends = $manifest['extends'];

                        if (!isset($this->_components[$extends])) {
                            throw new \RuntimeException(sprintf('Component: %s not found', $extends));
                        }

                        if(isset($manifest['identifier']))
                        {
                            $identifier = $manifest['identifier'];

                            //Append paths
                            $this->_components[$identifier] = array_merge(
                                $this->_components[$identifier],
                                $this->_components[$extends]
                            );

                            //Set the namespace
                            if (isset($manifest['namespace']))
                            {
                                $namespace = $manifest['namespace'];

                                //Append the namespace
                                $this->_namespaces[$identifier] = array_merge(
                                    $this->_namespaces[$identifier],
                                    $this->_components[$extends]
                                );
                            }
                        }
                        else
                        {
                            //Prepend paths
                            $this->_components[$extends] = array_merge(
                                $manifest['paths'],
                                $this->_components[$extends]
                            );

                            //Set the namespace
                            if (isset($manifest['namespace']))
                            {
                                $namespace = $manifest['namespace'];

                                //Prepend the namespace
                                $this->_namespaces[$extends] = array_merge(
                                    array($namespace => $manifest['paths']),
                                    $this->_namespaces[$extends]
                                );
                            }
                        }
                    }
                }

                foreach($this->_files as $path)
                {
                    $array = $factory->fromFile($path, false);

                    //Priority
                    if(isset($array['priority'])) {
                        $priority = $array['priority'];
                    } else {
                        $priority = self::PRIORITY_NORMAL;
                    }

                    //Aliases
                    if(isset($array['aliases']))
                    {
                        if(!isset($aliases[$priority])) {
                            $aliases[$priority] = array();
                        }

                        $aliases[$priority] = array_merge($aliases[$priority], $array['aliases']);;
                    }

                    //Identifiers
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
                $identifiers_flat = array();

                ksort($identifiers);
                foreach ($identifiers as $priority => $merges) {
                    $identifiers_flat = array_merge_recursive($identifiers_flat, $merges);
                }

                foreach ($identifiers_flat as $identifier => $config) {
                    $manager->setIdentifier(new ObjectIdentifier($identifier, $config));
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
                    $manager->registerAlias($identifier, $alias);
                }

                /*
                 * Reset the bootstrapper in the object manager
                 *
                 * If cache is enabled this will prevent the bootstrapper from reloading the config resources
                 */
                $identifier = new ObjectIdentifier('lib:object.bootstrapper', array(
                    'bootstrapped' => true,
                    'directories'  => $this->_directories,
                    'components'   => $this->_components,
                    'namespaces'   => $this->_namespaces,
                    'files'        => $this->_files,
                    'aliases'      => $aliases_flat,
                ));

                $manager->setIdentifier($identifier)
                    ->setObject('lib:object.bootstrapper', $this);
            }
            else
            {
                foreach($this->_aliases as $alias => $identifier) {
                    $manager->registerAlias($identifier, $alias);
                }
            }

            /*
             * Setup the component class locator
             *
             * Locators are always setup as the  cannot be cached in the registry objects.
             */
            foreach($this->_namespaces as $identifier => $namespaces)
            {
                //Register the namespace in the component class locator
                foreach($namespaces as $namespace => $paths) {
                    $manager->getClassLoader()->getLocator('component')->registerNamespace($namespace, $paths);
                }

                //Register the namespace in the component objects locator
                $manager->getLocator('component')->registerIdentifier($identifier, array_keys($namespaces));
            }

            $this->_bootstrapped = true;
        }
    }

    /**
     * Register components from a directory to be bootstrapped
     *
     * All the first level directories are assumed to be component folders and will be registered.
     *
     * @param string  $directory
     * @param bool    $bootstrap If TRUE bootstrap all the components in the directory. Default TRUE
     * @return ObjectBootstrapper
     */
    public function registerComponents($directory, $bootstrap = true)
    {
        if(!isset($this->_directories[$directory]))
        {
            foreach (new \DirectoryIterator($directory) as $dir)
            {
                //Only get the component directory names
                if ($dir->isDot() || !$dir->isDir() || !preg_match('/^[a-zA-Z]+/', $dir->getBasename())) {
                    continue;
                }

                $this->registerComponent($dir->getPathname(), $bootstrap);
            }

            $this->_directories[$directory] = true;
        }

        return $this;
    }

    /**
     * Register a component to be bootstrapped.
     *
     * Class and object locators will be setup based on the 'bootstrap' information in the composer.json file.
     * If the component contains a /resources/config/bootstrapper.php file it will be registered.
     *
     * @param string $path          The component path
     * @param bool   $bootstrap     If TRUE bootstrap all the components in the directory. Default TRUE
     * @param array  $paths         Additional array of paths
     * @return ObjectBootstrapper
     */
    public function registerComponent($path, $bootstrap = true, array $paths = array())
    {
        if(!isset($this->_manifests[$path]) && file_exists($path.'/component.json'))
        {
            $manifest = $this->getObject('object.config.factory')->fromFile($path . '/component.json', false);

            //Register the manifest
            if (isset($manifest['bootstrap']))
            {
                array_unshift($paths, $path);
                $manifest['bootstrap']['paths'] = $paths;

                $this->_manifests[$path] = $manifest['bootstrap'];
            }

            //Register the config file
            if ($bootstrap && file_exists($path . '/resources/config/bootstrapper.php')) {
                $this->registerFile($path . '/resources/config/bootstrapper.php');
            }
        }

        return $this;
    }

    /**
     * Register a configuration file to be bootstrapped
     *
     * @param string $path  The absolute path to the file
     * @return ObjectBootstrapper
     */
    public function registerFile($path)
    {
        $hash = md5($path);

        if(!isset($this->_files[$hash])) {
            $this->_files[$hash] = $path;
        }

        return $this;
    }

    /**
     * Get the registered components
     *
     * @param string $domain  The component domain. Domain is optional and can be NULL
     * @return array
     */
    public function getComponents($domain = null)
    {
        $components = $result = array_keys($this->_components);

        if($domain)
        {
            foreach($components as $key => $component)
            {
                if(strpos($component, 'com://'.$domain) === false) {
                    unset($components[$key]);
                }
            }
        }

        return $components;
    }

    /**
     * Get a hash based on a name and domain
     *
     * @param string $name    The component name
     * @param string $domain  The component domain. Domain is optional and can be NULL
     * @return string The hash
     */
    public function getComponentIdentifier($name, $domain = null)
    {
        if($domain && ($domain != $name)) {
            $hash = 'com://'.$domain.'/'.$name;
        } else {
            $hash = 'com:'.$name;
        }

        return $hash;
    }

    /**
     * Get a registered component path
     *
     * @param string $name    The component name
     * @param string $domain  The component domain. Domain is optional and can be NULL
     * @return array Returns the component path(s) if the component is registered. FALSE otherwise
     */
    public function getComponentPaths($name, $domain = null)
    {
        $result = array();

        $identifier = $this->getComponentIdentifier($name, $domain);
        if(isset($this->_components[$identifier])) {
            $result = $this->_components[$identifier];
        }

        return $result;
    }

    /**
     * Get manifest for a registered component
     *
     * @link https://en.wikipedia.org/wiki/Manifest_file
     *
     * @param string $name    The component name
     * @param string $domain  The component domain. Domain is optional and can be NULL
     * @return ObjectConfigJson|false Returns the component manifest or FALSE if the component couldn't be found.
     */
    public function getComponentManifest($name, $domain = null)
    {
        $result = false;
        $paths  = $this->getComponentPaths($name, $domain);

        if(!empty($paths))
        {
            $path = $paths[0];

            if(!isset($this->_manifests[$path]) || is_array($this->_manifests[$path]))
            {
                if($paths = $this->getComponentPaths($name, $domain))
                {
                    $info = $this->getObject('object.config.factory')->fromFile($path . '/component.json');
                    $this->_manifests[$path] = $info;
                }
                else $this->_manifests[$path] = false;
            }

            $result = $this->_manifests[$path];
        }

        return $result;
    }

    /**
     * Check if the bootstrapper has been run
     *
     * If you specify a specific component name the function will check if this component was bootstrapped.
     *
     * @param string $name    The component name
     * @param string $domain  The component domain. Domain is optional and can be NULL
     * @return bool TRUE if the bootstrapping has run FALSE otherwise
     */
    public function isBootstrapped($name = null, $domain = null)
    {
        if($name)
        {
            $identifier = $this->getComponentIdentifier($name, $domain);
            $result = $this->_bootstrapped && isset($this->_components[$identifier]);
        }
        else $result = $this->_bootstrapped;

        return $result;
    }
}
