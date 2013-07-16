<?php
/**
* @version		$Id$
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Alias filter
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class ComKoowaFilterAlias extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	string	Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return true;
	}

	/**
	 * Sanitize a value
	 *
	 * @param	string Variable to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		if (JFactory::getApplication()->getCfg('unicodeslugs') == 1) {
            $value = JFilterOutput::stringURLUnicodeSlug($value);
		}
		else {
            $value = JFilterOutput::stringURLSafe($value);
		}

		return $value;
	}
}