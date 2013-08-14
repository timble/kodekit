<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Alphabetic Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterAlpha extends KFilterAbstract
{
	/**
	 * Validate a variable
	 *
	 * @param	mixed	$value Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);

		return ctype_alpha($value);
	}

	/**
	 * Sanitize a variable
	 *
	 * @param	mixed	$value Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$pattern 	= '/[^[a-zA-Z]*/';
    	return preg_replace($pattern, '', $value);
	}
}
