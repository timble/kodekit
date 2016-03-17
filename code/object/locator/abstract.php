<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Locator
 */
abstract class KObjectLocatorAbstract extends KObject implements KObjectLocatorInterface
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = '';

    /**
     * Locator identifiers
     *
     * @var array
     */
    protected $_identifiers = array();

    /**
     * Returns a fully qualified class name for a given identifier.
     *
     * @param KObjectIdentifier $identifier An identifier object
     * @param bool  $fallback   Use the fallback sequence to locate the identifier
     * @return string|false  Return the class name on success, returns FALSE on failure
     */
    public function locate(KObjectIdentifier $identifier, $fallback = true)
    {
        $domain  = empty($identifier->domain) ? 'koowa' : ucfirst($identifier->domain);
        $package = ucfirst($identifier->package);
        $path    = KStringInflector::camelize(implode('_', $identifier->path));
        $file    = ucfirst($identifier->name);
        $class   = $path.$file;

        $info = array(
            'identifier' => $identifier,
            'class'      => $class,
            'package'    => $package,
            'domain'     => $domain,
            'path'       => $path,
            'file'       => $file,
        );

        return $this->find($info, $fallback);
    }

    /**
     * Find a class
     *
     * @param array  $info      The class information
     * @param bool   $fallback  If TRUE use the fallback sequence
     * @return bool|mixed
     */
    public function find(array $info, $fallback = true)
    {
        $result = false;
        $missed = array();

        //Get the class templates
        if(!empty($info['domain'])) {
            $identifier = $this->getName().'://'.$info['domain'].'/'.$info['package'];
        } else {
            $identifier = $this->getName().':'.$info['package'];
        }

        $templates = $this->getClassTemplates(strtolower($identifier));

        //Find the class
        foreach($templates as $template)
        {
            $class = str_replace(
                array('<Domain>',      '<Package>'     ,'<Path>'      ,'<File>'      , '<Class>'),
                array($info['domain'], $info['package'], $info['path'], $info['file'], $info['class']),
                $template
            );

            //Do not try to locate a class twice
            if(!isset($missed[$class]) && class_exists($class))
            {
                $result = $class;
                break;
            }

            if(!$fallback) {
                break;
            }

            //Mark the class
            $missed[$class] = false;
        }

        return $result;
    }

    /**
     * Get the list of class templates for an identifier
     *
     * @param string $identifier The package identifier
     * @return array The class templates for the identifier
     */
    abstract public function getClassTemplates($identifier);

    /**
     * Register an identifier
     *
     * @param  string       $identifier
     * @param  string|array $namespace(s) Sequence of fallback namespaces
     * @return KObjectLocatorAbstract
     */
    public function registerIdentifier($identifier, $namespaces)
    {
        $this->_identifiers[$identifier] = (array) $namespaces;
        return $this;
    }

    /**
     * Get the namespace(s) for the identifier
     *
     * @param string $identifier The package identifier
     * @return array|false The namespace(s) or FALSE if the identifier does not exist.
     */
    public function getIdentifierNamespaces($identifier)
    {
        return isset($this->_identifiers[$identifier]) ?  $this->_identifiers[$identifier] : false;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public static function getName()
    {
        return static::$_name;
    }
}
