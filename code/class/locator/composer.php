<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Composer Class Locator
 *
 * Proxy calls to the Composer Autoloader through Composer\Autoload\ClassLoader::findFile().
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Class\Locator
 */
class KClassLocatorComposer extends KClassLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'composer';

    /**
     * The composer loader
     *
     * @var \Composer\Autoload\ClassLoader
     */
    protected $_loader = null;

    /**
     * Constructor
     *
     * @param array $config Array of configuration options.
     */
    public function __construct($config = array())
    {
        if(isset($config['vendor_path']))
        {
            if(file_exists($config['vendor_path'].'/autoload.php'))
            {
                //Let Nooku proxy class loading
                $this->_loader = require $config['vendor_path'].'/autoload.php';
            }
        }
    }

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string $class     The class name
     * @param  string $basepath  The base path
     * @return string|false Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function locate($class, $basepath = null)
    {
        $path = false;

        if($this->_loader) {
            $path = $this->_loader->findFile($class);
        }

        return $path;
    }
}
