<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Http Url Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http\Url
 */
interface KHttpUrlInterface
{
    /**
     * Parse the url from a string
     *
     * Partial URLs are also accepted. setUrl() tries its best to parse them correctly. Function also accepts an
     * associative array like parse_url returns.
     *
     * @param   string|array  $url Part(s) of an URL in form of a string or associative array like parse_url() returns
     * @throws  \UnexpectedValueException If the url is not an array a string or cannot be casted to one.
     * @return  KHttpUrl
     * @see     parse_url()
     */
    public function setUrl($url);

    /**
     * Get the scheme part of the URL
     *
     * @return string|null
     */
    public function getScheme();

    /**
     * Set the URL scheme
     *
     * @param  string $scheme
     * @return  KHttpUrl
     */
    public function setScheme($scheme);

    /**
     * Get the URL user
     *
     * @return string|null
     */
    public function getUser();

    /**
     * Set the URL user
     *
     * @param  string $user
     * @return KHttpUrl
     */
    public function setUser($user);

    /**
     * Get the URL password
     *
     * @return string|null
     */
    public function getPass();

    /**
     * Set the URL password
     *
     * @param  string $pass
     * @return KHttpUrl
     */
    public function setPass($pass);

    /**
     * Get the URL host
     *
     * @return string|null
     */
    public function getHost();

    /**
     * Set the URL Host
     *
     * @param  string $host
     * @return KHttpUrl
     */
    public function setHost($host);

    /**
     * Get the URL port
     *
     * @return integer|null
     */
    public function getPort();

    /**
     * Set the port part of the URL
     *
     * @param  integer $port
     * @return KHttpUrl
     */
    public function setPort($port);

    /**
     * Returns the path portion as a string or array
     *
     * @param     boolean $toArray If TRUE return an array. Default FALSE
     * @return  string|array The path string; e.g., `path/to/site`.
     */
    public function getPath($toArray = false);

    /**
     * Sets the HttpUrl::$path array and $format from a string.
     *
     * This will overwrite any previous values. Also, resets the format based on the final path value.
     *
     * @param   string|array  $path The path string or array of elements to use; for example,"/foo/bar/baz/dib".
     *                              A leading slash will *not* create an empty first element; if the string has a
     *                              leading slash, it is ignored.
     * @return  KHttpUrl
     */
    public function setPath($path);

    /**
     * Returns the query portion as a string or array
     *
     * @param   boolean      $toArray If TRUE return an array. Default FALSE
     * @param   boolean|null $escape  If TRUE escapes '&' to '&amp;' for xml compliance. If NULL use the default.
     * @return  string|array The query string; e.g., `foo=bar&baz=dib`.
     */
    public function getQuery($toArray = false, $escape = null);

    /**
     * Sets the query string
     *
     * If an string is provided, will decode the string to an array of parameters. Array values will be represented in
     * the query string using PHP's common square bracket notation.
     *
     * @param   string|array  $query  The query string to use; for example `foo=bar&baz=dib`.
     * @param   boolean       $merge  If TRUE the data in $query will be merged instead of replaced. Default FALSE.
     * @return  KHttpUrl
     */
    public function setQuery($query, $merge = false);

    /**
     * Get the URL fragment
     *
     * @return string|null
     */
    public function getFragment();

    /**
     * Set the URL fragment part
     *
     * @param  string $fragment
     * @return KHttpUrl
     */
    public function setFragment($fragment);

    /**
     * Enable/disable URL escaping
     *
     * @param bool $escape
     * @return KHttpUrlInterface
     */
    public function setEscape($escape);

    /**
     * Get the escape setting
     *
     * @return bool
     */
    public function getEscape();

    /**
     * Build the url from an array
     *
     * @param   array  $parts Associative array like parse_url() returns.
     * @return  KHttpUrl
     * @see     parse_url()
     */
    public static function fromArray(array $parts);

    /**
     * Build the url from a string
     *
     * Partial URLs are also accepted. fromString tries its best to parse them correctly.
     *
     * @param   string  $url
     * @throws  \UnexpectedValueException If the url is not a string or cannot be casted to one.
     * @return  KHttpUrl
     * @see     parse_url()
     */
    public static function fromString($url);

    /**
     * Get the full url, of the format scheme://user:pass@host/path?query#fragment';
     *
     * @param integer      $parts   A bitmask of binary or'ed HTTP_URL constants; FULL is the default
     * @param boolean|null $escape  If TRUE escapes '&' to '&amp;' for xml compliance. If NULL use the default.
     * @return  string
     */
    public function toString($parts = self::FULL, $escape = false);

    /**
     * Check if two url's are equal
     *
     * @param KHttpUrlInterface $url
     * @return Boolean
     */
    public function equals(KHttpUrlInterface $url);
}
