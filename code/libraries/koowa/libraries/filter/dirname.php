<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Directory name Filter
 *
 * Calls {@link dirname()} on the passed value
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterDirname extends KFilterAbstract implements KFilterTraversable
{
	/**
	 * Validate a value
	 *
	 * @param	mixed	$value Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($value)
	{
		$value = trim($value);
	   	return ((string) $value === $this->sanitize($value));
	}

	/**
	 * Sanitize a value
	 *
	 * @param	mixed	$value Variable to be sanitized
	 * @return	string
	 */
	public function sanitize($value)
	{
		$value = trim($value);
    	return dirname($value);
	}
}
