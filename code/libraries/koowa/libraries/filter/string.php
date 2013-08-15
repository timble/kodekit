<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * String Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterString extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	mixed	$value Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		$value = trim($value);
		return (is_string($value) && ($value === filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
	}

	/**
	 * Sanitize a value
	 *
	 * @param	mixed	$value Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		return filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	}
}

