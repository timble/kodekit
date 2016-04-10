<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Http Url Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Url
 */
interface HttpUrlInterface extends \Serializable
{
    /**
     * The url parts
     *
     * @see toString()
     */
    const SCHEME   = 1;
    const USER     = 2;
    const PASS     = 4;
    const HOST     = 8;
    const PORT     = 16;
    const PATH     = 32;
    const QUERY    = 64;
    const FRAGMENT = 128;

    const USERINFO  = 6;   //User info
    const AUTHORITY = 31;  //Authority
    const BASE      = 63;  //Hierarchical part
    const FULL      = 255; //Complete url

    /**
     * Parse the url from a string
     *
     * Partial URLs are also accepted. setUrl() tries its best to parse them correctly. Function also accepts an
     * associative array like parse_url returns.
     *
     * @param   string|array  $url Part(s) of an URL in form of a string or associative array like parse_url() returns
     * @throws  \UnexpectedValueException If the url is not an array a string or cannot be casted to one.
     * @return  HttpUrl
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
     * @return  HttpUrl
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
     * @return HttpUrl
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
     * @return HttpUrl
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
     * @return HttpUrl
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
     * @return HttpUrl
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
     * @return  HttpUrl
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
     * @return  HttpUrl
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
     * @return HttpUrl
     */
    public function setFragment($fragment);

    /**
     * Enable/disable URL escaping
     *
     * @param bool $escape If TRUE escapes '&' to '&amp;' for xml compliance
     * @return HttpUrlInterface
     */
    public function setEscaped($escape);

    /**
     * Get the escape setting
     *
     * @return bool If TRUE escapes '&' to '&amp;' for xml compliance
     */
    public function isEscaped();

    /**
     * Build the url from an array
     *
     * @param   array  $parts Associative array like parse_url() returns.
     * @return  HttpUrl
     * @see     parse_url()
     */
    public static function fromArray(array $parts);

    /**
     * Return the url components
     *
     * @param integer $parts   A bitmask of binary or'ed HTTP_URL constants; FULL is the default
     * @param boolean|null $escape  If TRUE escapes '&' to '&amp;' for xml compliance. If NULL use the default.
     * @return array Associative array like parse_url() returns.
     * @see parse_url()
     */
    public function toArray($parts = self::FULL, $escape = null);

    /**
     * Build the url from a string
     *
     * Partial URLs are also accepted. fromString tries its best to parse them correctly.
     *
     * @param   string  $url
     * @throws  \UnexpectedValueException If the url is not a string or cannot be casted to one.
     * @return  HttpUrl
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
     * @param HttpUrlInterface $url
     * @return Boolean
     */
    public function equals(HttpUrlInterface $url);
}
