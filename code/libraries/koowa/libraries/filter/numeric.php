<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Numeric Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterNumeric extends KFilterAbstract implements KFilterTraversable
{
	/**
	 * Validate whether the given variable is numeric. Numeric strings consist of optional sign, any
 	 * number of digits, optional decimal part and optional exponential part. Thus +0123.45e6 is a
 	 * valid numeric value. Hexadecimal notation (0xFF) is allowed too but only without sign, decimal
 	 * and exponential part
	 *
	 * @param	mixed	$value Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($value)
	{
		return (is_string($value) && is_numeric($value));
	}

	/**
	 * Sanitize non-numeric characters from the value.
	 *
	 * @param	mixed	$value Value to be sanitized
	 * @return	float
	 */
	public function sanitize($value)
	{
		return (string) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT,
			FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND | FILTER_FLAG_ALLOW_SCIENTIFIC);
	}
}

