<?php
/**
 * @version 	$Id$
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for a plugin
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class KLoaderAdapterPlugin extends KLoaderAdapterAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'plg';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'Plg';

	/**
	 * Get the path based on a class name
	 *
	 * @param  string $classname The class name
     * @param  string $basepath  The base path
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
	{
		$path = false;

		$word  = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));
		$parts = explode(' ', $word);

        $type    = array_shift($parts);
        $package = array_shift($parts);

		if ($type == 'plg')
		{
			if(count($parts)) {
				$path = implode('/', $parts);
			} else {
				$path = $package;
			}

            //Find the basepath
            if(!empty($basepath)) {
                $this->_basepath = $basepath;
            }

            if(isset($this->_basepaths[$package])) {
                $basepath = $this->_basepaths[$package];
            } else {
                $basepath = $this->_basepath;
            }

		    $path = $basepath.'/plugins/'.$package.'/'.$path.'.php';
	    }

		return $path;

	}
}