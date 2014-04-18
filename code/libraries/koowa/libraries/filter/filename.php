<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Filename Filter
 *
 * Filter strips path info
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterFilename extends KFilterAbstract implements KFilterTraversable
{
	/**
	 * Validate a value
	 *
	 * @param	mixed	$value Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($value)
	{
	   	return ((string) $value === $this->sanitize($value));
	}

	/**
	 * Sanitize a value
	 *
	 * @param	mixed	$value Value to be sanitized
	 * @return	string
	 */
	public function sanitize($value)
	{
        // basename does not work if the string starts with a UTF character
        return ltrim(basename(strtr($value, array('/' => '/ '))));
	}
}
