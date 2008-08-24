<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Raw filter
 *
 * Always validates and returns the raw variable
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Filter
 * @version     1.0
 */
class KFilterRaw implements KFilterInterface
{
	/**
	 * Validate a variable
	 *
	 * @param	scalar	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($var)
	{
		return true;
	}
	
	/**
	 * Sanitize a variable
	 *
	 * @param	scalar	Variable to be sanitized
	 * @return	scalar
	 */
	public function sanitize($var)
	{
		return $var;
	}
}