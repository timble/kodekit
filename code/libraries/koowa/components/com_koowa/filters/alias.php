<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Alias Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaFilterAlias extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	string	$value Variable to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return true;
	}

	/**
	 * Sanitize a value
	 *
	 * @param	string $value Variable to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		if (JFactory::getApplication()->getCfg('unicodeslugs') == 1) {
            $value = JFilterOutput::stringURLUnicodeSlug($value);
		} else {
            $value = JFilterOutput::stringURLSafe($value);
		}

		return $value;
	}
}
