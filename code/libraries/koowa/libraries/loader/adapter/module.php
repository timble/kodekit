<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Loader Adapter for a plugin
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 * @uses		KInflector
 */
class KLoaderAdapterModule extends KLoaderAdapterAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'mod';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'Mod';

	/**
	 * Get the path based on a class name
	 *
	 * @param  string $classname    The class name
     * @param  string $basepath     The base path
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
	{
		$path = false;

        if (substr($classname, 0, strlen($this->_prefix)) === $this->_prefix)
        {
            /*
             * Exception rule for Exception classes
             *
             * Transform class to lower case to always load the exception class from the /exception/ folder.
             */
            if ($pos = strpos($classname, 'Exception')) {
                $filename  = substr($classname, $pos + strlen('Exception'));
                $classname = str_replace($filename, ucfirst(strtolower($filename)), $classname);
            }

            $word  = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));
            $parts = explode(' ', $word);

            $type    = array_shift($parts);
            $package = array_shift($parts);

		    $module = 'mod_'.$package;
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
            if(!empty($basepath) && empty($this->_basepaths[$package])) {
                $this->_basepath = $basepath;
            }

            if(isset($this->_basepaths[$package])) {
                $basepath = $this->_basepaths[$package];
            } else {
                $basepath = $this->_basepath;
            }

			$path = $basepath.'/modules/'.$module.'/'.$path.'.php';
		}

		return $path;

	}
}
