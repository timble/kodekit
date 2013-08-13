<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Http Url Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http
 */
interface KHttpUrlInterface
{
    /**
     * Get the full url, of the format scheme://user:pass@host/path?query#fragment';
     *
     * @param integer $parts A bitmask of binary or'ed HTTP_URL constants; FULL is the default
     * @return  string
     */
    public function toString($parts = self::FULL);

    /**
     * Set the url
     *
     * @param   string  $url url
     * @return  KHttpUrl
     */
    public static function fromString($url);

    /**
     * Sets the query string in the url, for KHttpUrl::getQuery() and KHttpUrl::$query.
     *
     * This will overwrite any previous values.
     *
     * @param   string|array    The query string to use; for example `foo=bar&baz=dib`.
     * @return  KHttpUrl
     */
    public function setQuery($query);

    /**
     * Returns the query portion as a string or array
     *
     * @param 	boolean			$toArray If TRUE return an array. Default FALSE
     * @return  string|array    The query string; e.g., `foo=bar&baz=dib`.
     */
    public function getQuery($toArray = false);

    /**
     * Sets the KHttpUrl::$path array and $format from a string.
     *
     * This will overwrite any previous values. Also, resets the format based
     * on the final path value.
     *
     * @param   string  $path The path string to use; for example,"/foo/bar/baz/dib".
     * A leading slash will *not* create an empty first element; if the string
     * has a leading slash, it is ignored.
     * @return  KHttpUrl
     */
    public function setPath($path);
}
