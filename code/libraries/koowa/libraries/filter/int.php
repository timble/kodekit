<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Integer Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterInt extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	$value Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return empty($value) || (false !== filter_var($value, FILTER_VALIDATE_INT));
	}

	/**
	 * Sanitize a value
	 *
	 * @param	scalar	$value Value to be sanitized
	 * @return	int
	 */
	protected function _sanitize($value)
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}
}

