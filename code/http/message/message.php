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
 * Http Message
 *
 * @see http://tools.ietf.org/html/rfc2616#section-4
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Message
 */
abstract class HttpMessage extends ObjectAbstract implements HttpMessageInterface
{
    /**
     * The message headers
     *
     * @var HttpMessageHeaders
     */
    protected $_headers;

    /**
     * The http version
     *
     * @var string
     */
    protected $_version;

    /**
     * The message content
     *
     * @var string
     */
    protected $_content;

    /**
     * The message content type
     *
     * @var string
     */
    protected $_content_type;

    /**
     * Mediatype to format mappings
     *
     * @var array
     */
    protected static $_formats;

    /**
     * Constructor
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Define the message formats
        self::$_formats = ObjectConfig::unbox($config->formats);

        //Set Headers
        $this->setHeaders($config->headers);

        $this->setVersion($config->version);
        $this->setContent($config->content);
        $this->setContentType($config->content_type);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'version'      => '1.1',
            'content'      => '',
            'content_type' => '',
            'headers'      => array(),
            'formats'      => array(
                'html'       => array('text/html', 'application/xhtml+xml'),
                'txt'        => array('text/plain'),
                'csv'        => array('text/csv'),
                'js'         => array('application/javascript', 'application/x-javascript', 'text/javascript'),
                'css'        => array('text/css'),
                'json'       => array('application/json', 'application/x-json', 'application/vnd.api+json'),
                'xml'        => array('text/xml', 'application/xml', 'application/x-xml'),
                'rdf'        => array('application/rdf+xml'),
                'atom'       => array('application/atom+xml'),
                'rss'        => array('application/xml', 'application/rss+xml'),
                'jsonstream' => array('application/stream+json'),
                'binary'     => array('application/octet-stream'),
            ),

        ));

        parent::_initialize($config);
    }

    /**
     * Set the header parameters
     *
     * @param  array $headers
     * @return HttpMessageInterface
     */
    public function setHeaders($headers)
    {
        $this->_headers = $this->getObject('lib:http.message.headers', array('headers' => $headers));
        return $this;
    }

    /**
     * Get the headers container
     *
     * @return HttpMessageHeaders
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Sets the HTTP protocol version (1.0 or 1.1).
     *
     * @param string $version The HTTP protocol version
     * @throws \InvalidArgumentException
     * @return HttpMessage
     */
    public function setVersion($version)
    {
        if ($version != '1.1' && $version != '1.0') {
            throw new \InvalidArgumentException('Not valid or not supported HTTP version: ' . $version);
        }

        $this->_version = $version;
        return $this;
    }

    /**
     * Gets the HTTP protocol version.
     *
     * @return string The HTTP protocol version
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Sets the response content.
     *
     * Valid types are strings, numbers, and objects that implement a __toString() method.
     *
     * @param mixed  $content   The content
     * @param string $type      The content type
     * @throws \UnexpectedValueException If the content is not a string are cannot be casted to a string.
     * @return HttpMessage
     */
    public function setContent($content, $type = null)
    {
        if (!is_null($content) && !is_string($content) && !(is_object($content) && method_exists($content, '__toString')))
        {
            throw new \UnexpectedValueException(
                'The message content must be a string or object implementing __toString(), "'.gettype($content).'" given.'
            );
        }

        //Cast to a string
        $this->_content = $content;

        if(isset($type)) {
            $this->setContentType($type);
        }

        return $this;
    }

    /**
     * Get message content
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets the response content type
     *
     * @see http://tools.ietf.org/html/rfc2616#section-14.17
     *
     * @param string $type Content type
     * @return HttpMessage
     */
    public function setContentType($type)
    {
        if($type)
        {
            $this->_content_type = $type;
            $this->_headers->set('Content-Type', array($type => array('charset' => 'utf-8')));
        }

        return $this;
    }

    /**
     * Retrieves the message content type

     * @link http://tools.ietf.org/html/rfc2616#section-14.17
     *
     * @return string The content type
     */
    public function getContentType()
    {
        if (empty($this->_content_type) && $this->_headers->has('Content-Type'))
        {
            $type = $this->_headers->get('Content-Type');

            //Strip parameters from content-type like "; charset=UTF-8"
            if (is_string($type))
            {
                if (preg_match('/^([^,\;]*)/', $type, $matches)) {
                    $type = $matches[1];
                }
            }

            $this->_content_type = $type;
        }

        return $this->_content_type;
    }

    /**
     * Return the message format
     *
     * @return  string  The message format NULL if no format could be found
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Sets a format
     *
     * @param string $format The format
     * @throws \UnexpectedValueException If the format hasn't been registered.
     * @return HttpMessage
     */
    public function setFormat($format)
    {
        if($format)
        {
            if(!isset(static::$_formats[$format])) {
                throw new \UnexpectedValueException('Unregistered format: "' . $format . '" given.');
            }

            $this->_format = $format;
        }

        return $this;
    }



    /**
     * Render the message as a string
     *
     * @return string
     */
    public function toString()
    {
        return $this->getContent();
    }

    /**
     * Allow PHP casting of this object
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->toString();
    }

    /**
     * Deep clone of this instance
     *
     * @return void
     */
    public function __clone()
    {
        $this->_headers = clone $this->_headers;
    }
}