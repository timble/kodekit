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
 * Http Client
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Client
 */
interface  HttpClientInterface
{
    /**
     * Send a http request
     *
     * @param  HttpRequestInterface $request   The http request object
     * @throws \RuntimeException If the request failed
     * @return  HttpResponseInterface
     */
    public function send(HttpRequestInterface $request);

    /**
     * Send a GET request
     *
     * If successfull and the response content format is known, the content will returned as an array, if the content
     * cannot be unserialised it will be returned directly. If the request fails FALSE will be returned.
     *
     * @link https://tools.ietf.org/html/rfc7231#page-24
     *
     * @param string $url  The endpoint url
     * @param array $headers Optional request headers
     * @return array|string|false
     */
    public function get($url, $headers = array());

    /**
     * Send a POST request
     *
     * If successfull and the response content format is known, the content will returned as an array, if the content
     * cannot be unserialised it will be returned directly. If the request fails FALSE will be returned.
     *
     * @link https://tools.ietf.org/html/rfc7231#page-25
     *
     * @param string $url  The endpoint url
     * @param array|ObjectConfigFormat $data The data to send. If the data is an array it will be urlencoded.
     * @param array $headers Optional request headers
     * @return array|string|false
     */
    public function post($url, $data, $headers = array());

    /**
     * Send a PUT request
     *
     * If successfull and the response content format is known, the content will returned as an array, if the content
     * cannot be unserialised it will be returned directly. If the request fails FALSE will be returned.
     *
     * @link https://tools.ietf.org/html/rfc7231#page-26
     *
     * @param string $url  The endpoint url
     * @param array|ObjectConfigFormat $data The data to send. If the data is an array it will be urlencoded.
     * @param array $headers Optional request headers
     * @return array|string|false
     */
    public function put($url, $data, $headers = array());

    /**
     * Send a DELETE request
     *
     * If successfull and the response content format is known, the content will returned as an array, if the content
     * cannot be unserialised it will be returned directly. If the request fails FALSE will be returned.
     *
     * @link https://tools.ietf.org/html/rfc7231#page-29
     *
     * @param string $url  The endpoint url
     * @param array|ObjectConfigFormat $data The data to send. If the data is an array it will be urlencoded.
     * @param array $headers Optional request headers
     * @return array|string|false
     */
    public function delete($url, $data = array(), $headers = array());

    /**
     * Send a OPTIONS request
     *
     * If successfull the response headers will returned as an array. If the request fails FALSE will be returned.
     *
     * @link https://tools.ietf.org/html/rfc7231#page-31
     *
     * @param string $url  The endpoint url
     * @param array $headers Optional request headers
     * @return array|false
     */
    public function options($url, $headers = array());

    /**
     * Send a HEAD request
     *
     * If successfull the response headers will returned as an array. If the request fails FALSE will be returned.
     *
     * @link https://tools.ietf.org/html/rfc7231#page-25
     *
     * @param string $url  The endpoint url
     * @param array $headers Optional request headers
     * @return array|false
     */
    public function head($url, $headers = array());
}
