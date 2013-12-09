<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Module Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectLocatorModule extends KObjectLocatorAbstract
{
	/**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'mod';

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
            'fallbacks' => array(
                'Mod<Package><Path><Name>',
                'ModKoowa<Path><Name>',
                'ModKoowa<Path>Default',
                'ComKoowa<Path><Name>',
                'ComKoowa<Path>Default',
                'K<Path><Name>',
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
        $name    = ucfirst($identifier->name);

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
            $path = $name;
            $name = '';
        }

        //Check if the class exists
        $result = false;
        if (!$this->getObject('koowa:class.loader')->loadClass('Mod'.$package.$class, $identifier->basepath))
        {
            //Use the fallbacks
            if($fallback)
            {
                foreach($this->_fallbacks as $fallback)
                {
                    $result = str_replace(
                        array('<Package>', '<Path>', '<Name>', '<Class>'),
                        array($package   , $path   , $name   , $class),
                        $fallback
                    );

                    if(!class_exists($result)) {
                        $result = false;
                    } else {
                        break;
                    }
                }
            }
        }
        else $result = 'Mod'.$package.$class;

        return $result;
    }
}
