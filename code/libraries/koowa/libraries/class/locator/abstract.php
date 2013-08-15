<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Loader Adapter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
abstract class KClassLocatorAbstract implements KClassLocatorInterface
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = '';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = '';

    /**
     * The active basepath
     *
     * @var string
     */
    protected $_basepath = '';

    /**
     * Package/basepath pairs to search
     *
     * @var array
     */
    protected $_basepaths = array();

	/**
     * Constructor.
     *
     * @param  array  $config An optional array with configuration options.
     */
    public function __construct( $config = array())
    {
        if(isset($config['basepaths']))
        {
            $packages = (array) $config['basepaths'];
            foreach($packages as $package => $path) {
                $this->registerBasepath($path, $package);
            }
        }
    }

    /**
     * Register a specific package basepath
     *
     * @param  string   $basepath The base path of the package
     * @param  string   $package
     * @return KClassLocatorInterface
     */
    public function registerBasepath($basepath, $package = '*')
    {
        if($package == '*') {
            $this->_basepath = $basepath;
        }

        $this->_basepaths[$package] = $basepath;
        return $this;
    }

	/**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType()
	{
		return $this->_type;
	}

    /**
     * Get the registered base paths
     *
     * @return array An array with package name as keys and base path as values
     */
    public function getBasepaths()
    {
        return $this->_basepaths;
    }

	/**
	 * Get the class prefix
	 *
	 * @return string	Returns the class prefix
	 */
	public function getPrefix()
	{
		return $this->_prefix;
	}
}
