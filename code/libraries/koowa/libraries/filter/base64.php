<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Base64 Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterBase64 extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
     * @param   mixed  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        $pattern = '#^[a-zA-Z0-9/+]*={0,2}$#';
        return (is_string($value) && preg_match($pattern, $value) == 1);
    }

	/**
     * Sanitize a value
     *
     * @param   mixed  $value Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        $value = trim($value);
        $pattern = '#[^a-zA-Z0-9/+=]#';
        return preg_replace($pattern, '', $value);
    }
}
