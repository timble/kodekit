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
 * Manifest Object
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Manifest
 */
class Manifest extends Object implements ManifestInterface
{
    /**
     * The manifest object
     *
     * @var ObjectConfigJson
     */
    private $__manifest;

    /**
     * Constructor
     *
     * @param   ObjectConfig $config Configuration options.
     */
    public function __construct( ObjectConfig $config)
    {
        parent::__construct($config);

        $package = $this->getIdentifier()->package;
        $domain  = $this->getIdentifier()->domain;

        $this->__manifest = $this->getObject('object.bootstrapper')
            ->getComponentManifest($package, $domain);
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the version
     *
     * See @link http://semver.org/spec/v2.0.0.html
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the license
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Get the copyright
     *
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * Get the homepage
     *
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Get the homepage
     *
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors->toArray();
    }

    /**
     * Retrieve a manifest option
     *
     * @param string $name
     * @return mixed
     */
    final public function __get($name)
    {
        return $this->__manifest->get($name, '');
    }

    /**
     * Test existence of a manifest option
     *
     * @param string $name
     * @return bool
     */
    final public function __isset($name)
    {
        return $this->__manifest->has($name);
    }

    /**
     * Implement dynamic getters
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return  ViewAbstract
     */
    final public function __call($method, $args)
    {
        if (count($args) == 0 && substr($method, 0, 2) == 'get')
        {
            $key = strtolower(substr($method, 3));
            return $this->$key;
        }

        return parent::__call($method, $args);
    }
}
