<?php
/**
 * @version 	$Id$
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for a component
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class KLoaderAdapterComponent extends KLoaderAdapterAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'com';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'Com';

	/**
	 * Get the path based on a class name
	 *
	 * @param  string $classname The class name
     * @param  string $basepath  The base path
	 * @return string|bool  	 Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
	{
		$path = false;

		$word    = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));
		$parts   = explode(' ', $word);

        $type    = array_shift($parts);
        $package = array_shift($parts);

		if ($type == 'com')
		{
		    $component = 'com_'.$package;
			$file 	   = array_pop($parts);

			if(count($parts))
			{
			    if($parts[0] != 'view')
			    {
			        foreach($parts as $key => $value) {
					    $parts[$key] = KInflector::pluralize($value);
				    }
			    }
			    else $parts[0] = KInflector::pluralize($parts[0]);

				$path = implode('/', $parts);
				$path = $path.'/'.$file;
			}
			else $path = $file;

            //Find the basepath
            if(!empty($basepath)) {
                $this->_basepath = $basepath;
            }

            if(isset($this->_basepaths[$package])) {
                $basepath = $this->_basepaths[$package];
            } else {
                $basepath = $this->_basepath;
            }

            $path = $basepath.'/components/'.$component.'/'.$path.'.php';
		}

		return $path;
	}
}