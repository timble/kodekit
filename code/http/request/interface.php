<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Http Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Request
 */
interface HttpRequestInterface extends HttpMessageInterface
{
    /**
     * Set the method for this request
     *
     * @param  string $method
     * @throws \InvalidArgumentException
     * @return HttpRequestInterface
     */
    public function setMethod($method);

    /**
     * Return the method for this request
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set the url for this request
     *
     * @param string|HttpUrl   $url
     * @throws \InvalidArgumentException If the url is not an instance of HttpUrl or a string
     * @return HttpRequest
     */
    public function setUrl($url);

    /**
     * Return the url for this request
     *
     * @return HttpUrl
     */
    public function getUrl();

    /**
     * Is this an OPTIONS method request?
     *
     * @return bool
     */
    public function isOptions();

    /**
     * Is this a GET method request?
     *
     * @return bool
     */
    public function isGet();

    /**
     * Is this a HEAD method request?
     *
     * @return bool
     */
    public function isHead();

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public function isPost();

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public function isPut();

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public function isDelete();

    /**
     * Is this a TRACE method request?
     *
     * @return bool
     */
    public function isTrace();

    /**
     * Is this a CONNECT method request?
     *
     * @return bool
     */
    public function isConnect();

    /*
     * Is this a PATCH method request?
     *
     * @return bool
     */
    public function isPatch();

    /**
     * Is the request an ajax request
     *
     * @return boolean
     */
    public function isAjax();

    /**
     * Is the request a flash request
     *
     * @return boolean
     */
    public function isFlash();
}