<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Boolean filter
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Filter
 * @version     1.0
 */
class KFilterBoolean implements KFilterInterface
{
	/**
	 * Validate a variable
	 * 
	 *  Returns TRUE for boolean values: "1", "true", "on" and "yes", "0", 
	 * "false", "off", "no", and "". Returns FALSE for all non-boolean values. 
	 *
	 * @param	scalar	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($var)
	{
		return (null !== filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) );
	}
	
	/**
	 * Sanitize a variable
	 * 
	 * Returns TRUE for "1", "true", "on" and "yes". Returns FALSE for all other values. 
	 *
	 * @param	scalar	Variable to be sanitized
	 * @return	scalar
	 */
	public function sanitize($var)
	{
		return filter_var($var, FILTER_VALIDATE_BOOLEAN);
	}
}