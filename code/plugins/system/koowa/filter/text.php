<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Text filter
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterText extends KObject implements KFilterInterface
{
	/**
	 * Validate a variable
	 * 
	 * NOTE: This should always be a simple yes/no question (is $var valid?), so 
	 * only true or false should be returned.
	 *
	 * @param	mixed	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($var)
	{
		return (is_string($var) && strcmp($var, $this->sanitize($var)) === 0);
	}
	
	/**
	 * Sanitize a variable
	 *
	 * @param	mixed	Variable to be sanitized
	 * @return	string
	 */
	public function sanitize($var)
	{
		$var = preg_replace( "'<script[^>]*>.*?</script>'si", '', $var );
		$var = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $var );
		$var = preg_replace( '/<!--.+?-->/', '', $var );
		$var = preg_replace( '/{.+?}/', '', $var );
		$var = preg_replace( '/&nbsp;/', ' ', $var );
		$var = preg_replace( '/&amp;/', ' ', $var );
		$var = preg_replace( '/&quot;/', ' ', $var );
		$var = strip_tags( $var );
		$var = htmlspecialchars( $var );
		return $var;
	}
}

