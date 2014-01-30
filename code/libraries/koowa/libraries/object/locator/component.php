<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class KObjectLocatorComponent extends KObjectLocatorAbstract
{
    /**
     * The type
     *
     * @var string
     */
    protected $_type = 'com';

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'sequence' => array(
                'Com<Package><Class>',
                'Com<Package><Path><File>',
                'ComKoowa<Path><File>',
                'ComKoowa<Path>Default',
                'K<Path><File>',
                'K<Path>Default'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Returns a fully qualified class name for a given identifier.
     *
     * @param KObjectIdentifier $identifier An identifier object
     * @param bool  $fallback   Use the fallbacks to locate the identifier
     * @return string|false  Return the class name on success, returns FALSE on failure
     */
    public function locate(KObjectIdentifier $identifier, $fallback = true)
    {
        $class   = KStringInflector::camelize(implode('_', $identifier->path)).ucfirst($identifier->name);

        $package = ucfirst($identifier->package);
        $file    = ucfirst($identifier->name);

        //Make an exception for 'view' and 'module' types
        $path  = $identifier->path;
        $type  = !empty($path) ? array_shift($path) : '';

        if(!in_array($type, array('view','module'))) {
            $path = ucfirst($type).KStringInflector::camelize(implode('_', $path));
        } else {
            $path = ucfirst($type);
        }

        //Allow locating default classes if $path is empty.
        if(empty($path))
        {
            $path = $file;
            $file = '';
        }

        $info = array(
            'class'   => $class,
            'package' => $package,
            'path'    => $path,
            'file'    => $file
        );

        return $this->find($info, $identifier->domain, $fallback);
    }
}
