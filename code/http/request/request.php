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
 * Http Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Request
 * @link    http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5
 */
class HttpRequest extends HttpMessage implements HttpRequestInterface
{
    // Methods
    const GET     = 'GET';
    const POST    = 'POST';
    const PUT     = 'PUT';
    const DELETE  = 'DELETE';
    const PATCH   = 'PATCH';
    const HEAD    = 'HEAD';
    const OPTIONS = 'OPTIONS';
    const TRACE   = 'TRACE';
    const CONNECT = 'CONNECT';

    /**
     * The request method
     *
     * @var string
     */
    protected $_method;

    /**
     * URL of the request regardless of the server
     *
     * @var HttpUrl
     */
    protected $_url;

    /**
     * Array of accepted media types
     *
     * @var KHttpUrl
     */
    protected $_accept;

    /**
     * Constructor
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return HttpRequest
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->setUrl($config->url);

        if(!empty($config->method)) {
            $this->setMethod($config->method);
        }
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'method'  => self::GET,
            'url'     => '',
            'headers' => array()
        ));

        parent::_initialize($config);
    }

    /**
     * Return the request format or mediatype
     *
     * Find the format by using following sequence :
     *
     * 1. Use the URL path extension
     * 2. Use the accept header with the highest quality apply the reverse format map to find the format.
     *
     * @return  string  The request format or NULL if no format could be found
     */
    public function getFormat()
    {
        if (!isset($this->_format))
        {
            $format = pathinfo($this->getUrl()->getPath(), PATHINFO_EXTENSION);

            if(empty($format) || !isset(static::$_formats[$format]))
            {
                $format = null; //reset

                if($accept = $this->getAccept())
                {
                    /**
                     * If the browser is requested text/html serve it at all times
                     *
                     * @hotfix #409 : Android 2.3 requesting application/xml
                     */
                    if (!isset($accept['text/html']))
                    {
                        //Get the highest quality format
                        $mime_type = key($accept);

                        foreach (static::$_formats as $value => $mime_types)
                        {
                            if (in_array($mime_type, (array)$mime_types)) {
                                $format = $value;
                                break;
                            }
                        }
                    }
                    else $format = 'html'; //html requested
                }
            }

            $this->_format = $format;
        }

        return $this->_format;
    }

    /**
     * Return the accept header
     *
     * Parses an accept header and returns an array (type => quality) of the accepted types, ordered by quality.
     *
     * @link : https://tools.ietf.org/html/rfc7231#page-38
     *
     * @param array   $defaults  The default values
     * @return array
     */
    public function getAccept(array $defaults = NULL)
    {
        if (!isset($this->_accept))
        {
            $accept = $this->_headers->get('Accept');

            if (!empty($accept))
            {
                // Get all of the types
                $types = explode(',', $accept);

                foreach ($types as $type)
                {
                    // Split the type into parts
                    $parts = explode(';', $type);

                    // Make the type only the MIME
                    $type = trim(array_shift($parts));

                    // Default quality is 1.0
                    $options = array('quality' => 1.0);

                    foreach ($parts as $part)
                    {
                        // Prevent undefined $value notice below
                        if (strpos($part, '=') === FALSE) {
                            continue;
                        }

                        // Separate the key and value
                        list ($key, $value) = explode('=', trim($part));

                        switch ($key)
                        {
                            case 'q'       : $options['quality'] = (float) trim($value); break;
                            case 'version' : $options['version'] = (float) trim($value); break;
                        }
                    }

                    // Add the accept type and quality
                    $defaults[$type] = $options;
                }
            }

            // Make sure that accepts is an array
            $accepts = (array) $defaults;

            // Order by quality
            arsort($accepts);

            $this->_accept = $accepts;
        }

        return $this->_accept;
    }

    /**
     * Get the cache control
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.2.1
     * @return array
     */
    public function getCacheControl()
    {
        $values = $this->_headers->get('Cache-Control', array());

        if (is_string($values)) {
            $values = array_map('trim', explode(',', $values));
        }

        foreach ($values as $key => $value)
        {
            if(is_string($value))
            {
                $parts = explode('=', $value);

                if (count($parts) > 1)
                {
                    unset($values[$key]);
                    $values[trim($parts[0])] = trim($parts[1]);
                }
            }
        }

        return $values;
    }

    /**
     * Set the header parameters
     *
     * @param  array $headers
     * @return HttpRequest
     */
    public function setHeaders($headers)
    {
        $this->_headers = $this->getObject('lib:http.request.headers', array('headers' => $headers));
        return $this;
    }

    /**
     * Set the method for this request
     *
     * @param  string $method
     * @throws \InvalidArgumentException
     * @return HttpRequest
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);

        if (!defined('static::'.$method)) {
            throw new \InvalidArgumentException('Invalid HTTP method passed');
        }

        $this->_method = $method;
        return $this;
    }

    /**
     * Return the method for this request
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Set the url for this request
     *
     * @param string|array  $url Part(s) of an URL in form of a string or associative array like parse_url() returns
     * @return HttpRequest
     */
    public function setUrl($url)
    {
        $this->_url = $this->getObject('lib:http.url', array('url' => $url));
        return $this;
    }

    /**
     * Return the Url of the request regardless of the server
     *
     * @return  HttpUrl A HttpUrl object
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Is this an OPTIONS method request?
     *
     * @return bool
     */
    public function isOptions()
    {
        return ($this->_method === self::OPTIONS);
    }

    /**
     * Is this a GET method request?
     *
     * @return bool
     */
    public function isGet()
    {
        return ($this->getMethod() === self::GET);
    }

    /**
     * Is this a HEAD method request?
     *
     * @return bool
     */
    public function isHead()
    {
        return ($this->getMethod() === self::HEAD);
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public function isPost()
    {
        return ($this->getMethod() === self::POST);
    }

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public function isPut()
    {
        return ($this->getMethod() === self::PUT);
    }

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public function isDelete()
    {
        return ($this->getMethod() === self::DELETE);
    }

    /**
     * Is this a TRACE method request?
     *
     * @return bool
     */
    public function isTrace()
    {
        return ($this->getMethod() === self::TRACE);
    }

    /**
     * Is this a CONNECT method request?
     *
     * @return bool
     */
    public function isConnect()
    {
        return ($this->getMethod() === self::CONNECT);
    }

    /**
     * Is this a PATCH method request?
     *
     * @return bool
     */
    public function isPatch()
    {
        return ($this->getMethod() === self::PATCH);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * @return boolean
     */
    public function isAjax()
    {
        $header = $this->_headers->get('X-Requested-With');
        return false !== $header && $header == 'XMLHttpRequest';
    }

    /**
     * Is the request a submitted HTTP form?
     *
     * @return boolean
     */
    public function isFormSubmit()
    {
        $form_submit = in_array($this->getContentType(), ['application/x-www-form-urlencoded', 'multipart/form-data']);

        return ($form_submit && !$this->isSafe() && !$this->isAjax());
    }

    /**
     * Is this a safe request?
     *
     * @link http://tools.ietf.org/html/rfc2616#section-9.1.1
     * @return boolean
     */
    public function isSafe()
    {
        return $this->isGet() || $this->isHead() || $this->isOptions();
    }

    /**
     * Is the request cacheable
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4.2.3
     * @return boolean
     */
    public function isCacheable()
    {
        return ($this->isGet() || $this->isHead()) && !in_array('no-store', $this->getCacheControl(), true);
    }

    /**
     * Render entire request as HTTP request string
     *
     * @return string
     */
    public function toString()
    {
        $request = sprintf('%s %s HTTP/%s', $this->getMethod(), (string) $this->getUrl(), $this->getVersion());

        $str = trim($request) . "\r\n";
        $str .= $this->getHeaders();
        $str .= "\r\n";
        $str .= $this->getContent();
        return $str;
    }

    /**
     * Deep clone of this instance
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();

        if($this->_url instanceof HttpUrl) {
            $this->_url = clone $this->_url;
        }
    }
}