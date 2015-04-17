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
     * The class prefix sequence in FIFO order
     *
     * @var array
     */
    protected $_sequence = array();

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_sequence = KObjectConfig::unbox($config->sequence);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional KObjectConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'sequence' => array(),
        ));

        parent::_initialize($config);
    }

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
            'file'       => $file
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

        //Find the class
        foreach($this->_sequence as $template)
        {
            $class = str_replace(
                array('<Package>'     ,'<Path>'      ,'<File>'      , '<Class>'),
                array($info['package'], $info['path'], $info['file'], $info['class']),
                $template
            );

            if(class_exists($class))
            {
                $result = $class;
                break;
            }

            if(!$fallback) {
                break;
            }
        }

        return $result;
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

    /**
     * Get the locator fallback sequence
     *
     * @return array
     */
    public function getSequence()
    {
        return $this->_sequence;
    }
}
