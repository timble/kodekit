<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Filter interface
 *
 * Validate or sanitize variables.
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Filter
 * @version     1.0
 */
interface KFilterInterface 
{
	/**
	 * Validate a variable
	 *
	 * NOTE: This should always be a simple yes/no question (is $var valid?), so 
	 * only true or false should be returned
	 * 
	 * @param	scalar	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($var);
	
	/**
	 * Sanitize a variable
	 *
	 * @param	scalar	Variable to be sanitized
	 * @return	scalar
	 */
	public function sanitize($var);
}