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
 * Abstract Dispatcher Response
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Dispatcher\Response
 */
abstract class DispatcherResponseAbstract extends ControllerResponse implements DispatcherResponseInterface
{
    /**
     * Stream resource
     *
     * @var FilesystemStreamInterface
     */
    private $__stream;

    /**
     * The transport queue
     *
     * @var	ObjectQueue
     */
    protected $_queue;

    /**
     * List of transport handlers
     *
     * @var array
     */
    protected $_transports;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config	An optional ObjectConfig object with configuration options.
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Create the transport queue
        $this->_queue = $this->getObject('lib:object.queue');

        //Attach the response transport handlers
        $transports = (array) ObjectConfig::unbox($config->transports);

        foreach ($transports as $key => $value)
        {
            if (is_numeric($key)) {
                $this->attachTransport($value);
            } else {
                $this->attachTransport($key, $value);
            }
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config    An optional ObjectConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'content'     => null,
            'transports'  => array('redirect', 'json', 'http'),
        ));

        parent::_initialize($config);
    }

    /**
     * Send the response
     *
     * Iterate through the response transport handlers. If a handler returns TRUE the chain will be stopped.
     *
     * @param  bool $terminate Whether to terminate the request by flushing it or not, defaults to TRUE
     * @return boolean  Returns true if the response has been send, otherwise FALSE
     */
    public function send($terminate = true)
    {
        foreach($this->_queue as $transport)
        {
            if($transport instanceof DispatcherResponseTransportInterface)
            {
                if($transport->send($this) == true)
                {
                    if($terminate) {
                        $this->terminate();
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Flush the output buffer and terminate request
     *
     * @return void
     */
    public function terminate()
    {
        //Cleanup and flush output to client
        if (!function_exists('fastcgi_finish_request'))
        {
            if (PHP_SAPI !== 'cli')
            {
                for ($i = 0; $i < ob_get_level(); $i++) {
                    ob_end_flush();
                }

                flush();
            }
        }
        else fastcgi_finish_request();

        //Set the exit status based on the status code.
        $status = 0;
        if(!$this->isSuccess()) {
            $status = (int) $this->getStatusCode();
        }

        exit($status);
    }

    /**
     * Sets the response content.
     *
     * If new content is set and a stream exists also reset the content in the stream.
     *
     * @param mixed  $content   The content
     * @param string $type      The content type
     * @throws \UnexpectedValueException If the content is not a string are cannot be casted to a string.
     * @return HttpMessage
     */
    public function setContent($content, $type = null)
    {
        //Refresh the buffer
        if($this->__stream instanceof FilesystemStreamInterface)
        {
            $this->__stream->truncate(0);
            $this->__stream->write((string) $content);
        }

        return parent::setContent($content, $type);
    }

    /**
     * Get the response stream
     *
     * The buffer://memory stream wrapper will be used when the response content is a string. If the response content
     * is of the form "scheme://..." a stream based on the scheme will be created.
     *
     * See @link http://www.php.net/manual/en/wrappers.php for a list of default PHP stream protocols and wrappers.
     *
     * @return FilesystemStreamInterface
     */
    public function getStream()
    {
        if(!isset($this->__stream))
        {
            $content = $this->getContent();
            $factory = $this->getObject('filesystem.stream.factory');

            if(!is_string($content) || !$this->getObject('filter.path')->validate($content))
            {
                $stream = $factory->createStream('kodekit-buffer://memory', 'w+b');
                $stream->write($content);
            }
            else $stream = $factory->createStream($content, 'rb');

            $this->__stream = $stream;
        }

        return $this->__stream;
    }


    /**
     * Sets the response content using a stream
     *
     * @param FilesystemStreamInterface $stream  The stream object
     * @return DispatcherResponseAbstract
     */
    public function setStream(FilesystemStreamInterface $stream)
    {
        $this->__stream = $stream;
        return $this;
    }

    /**
     * Get a transport handler by identifier
     *
     * @param   mixed $transport An object that implements ObjectInterface, ObjectIdentifier object
     *                                 or valid identifier string
     * @param   array $config An optional associative array of configuration settings
     * @throws \UnexpectedValueException
     * @return DispatcherResponseAbstract
     */
    public function getTransport($transport, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($transport) && strpos($transport, '.') === false)
        {
            $identifier = $this->getIdentifier()->toArray();

            if($identifier['package'] != 'dispatcher') {
                $identifier['path'] = array('dispatcher', 'response', 'transport');
            } else {
                $identifier['path'] = array('response', 'transport');
            }

            $identifier['name'] = $transport;
            $identifier = $this->getIdentifier($identifier);
        }
        else $identifier = $this->getIdentifier($transport);

        if (!isset($this->_transports[$identifier->name]))
        {
            $transport = $this->getObject($identifier, array_merge($config, array('response' => $this)));

            if (!($transport instanceof DispatcherResponseTransportInterface))
            {
                throw new \UnexpectedValueException(
                    "Transport handler $identifier does not implement DispatcherResponseTransportInterface"
                );
            }

            $this->_transports[$transport->getIdentifier()->name] = $transport;
        }
        else $transport = $this->_transports[$identifier->name];

        return $transport;
    }

    /**
     * Attach a transport handler
     *
     * @param   mixed  $transport An object that implements ObjectInterface, ObjectIdentifier object
     *                            or valid identifier string
     * @param   array $config  An optional associative array of configuration settings
     * @return DispatcherResponseAbstract
     */
    public function attachTransport($transport, $config = array())
    {
        if (!($transport instanceof DispatcherResponseTransportInterface)) {
            $transport = $this->getTransport($transport, $config);
        }

        //Enqueue the transport handler in the command chain
        $this->_queue->enqueue($transport, $transport->getPriority());

        return $this;
    }

    /**
     * Check if the response is streamable
     *
     * A response is considered streamable, if the Accept-Ranges does not have value 'none' or if the
     * Transfer-Encoding is set the chunked.
     *
     * If the request is made for a PDF file that is not attached the response will not be streamable.
     * The build in PDF viewer in IE and Chrome cannot handle inline rendering of PDF files when the
     * file is streamed.
     *
     * @link http://tools.ietf.org/html/rfc2616#section-14.5
     * @return bool
     */
    public function isStreamable()
    {
        $request = $this->getRequest();

        $isPDF        = (bool) $this->getContentType() == 'application/pdf';
        $isInline     = (bool) !$request->isDownload();
        $isSeekable   = (bool) $this->getStream()->isSeekable();

        if(!($isPDF && $isInline) && $isSeekable)
        {
            if($this->_headers->get('Transfer-Encoding') == 'chunked') {
                return true;
            }

            if($this->_headers->get('Accept-Ranges', null) !== 'none') {
                return true;
            };
        }

        return false;
    }

    /**
     * Check if the response is attachable
     *
     * A response is attachable if the request is downloadable or the content type is 'application/octet-stream'
     *
     * If the request is made by an Ipad, iPod or iPhone user agent the response will never be attachable. iOS browsers
     * cannot handle files send as disposition : attachment.
     *
     * If the request is made by MS Edge for a pdf file always force the response to be attachable to prevent 'Couldn't
     * open PDF file' errors in Edge.
     *
     * @return bool
     */
    public function isAttachable()
    {
        $request = $this->getRequest();

        if(!preg_match('#(iPad|iPod|iPhone)#', $request->getAgent()))
        {
            if($request->isDownload() || $this->getContentType() == 'application/octet-stream') {
                return true;
            }
        }

        if((preg_match('#(Edge)#', $request->getAgent())) )
        {
            if($this->getContentType() == 'application/pdf') {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the response is "stale".
     *
     * When the responses is stale, the response may not be served from cache without first re-validating with
     * the origin.
     *
     * @return Boolean true if the response is stale, false otherwise
     */
    public function isStale()
    {
        $cache_control = $this->getRequest()->getCacheControl();

        if(isset($cache_control['max-age']))
        {
            $maxAge = $cache_control['max-age'];
            $stale  = ($maxAge - $this->getAge()) <= 0;
        }
        else $stale = parent::isStale();

        return $stale;
    }

    /**
     * Returns true if the response is worth caching under any circumstance.
     *
     * Responses that cannot be stored or are without cache validation (Last-Modified, ETag) heades are
     * considered un-cacheable.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-3
     * @return Boolean true if the response is worth caching, false otherwise
     */
    public function isCacheable()
    {
        $result = false;

        if($this->getRequest()->isCacheable() && parent::isCacheable()) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check if the response is downloadable
     *
     * @return bool
     */
    public function isDownloadable()
    {
        if($this->isSuccess() && $this->getStream()->getType() == 'file') {
            return true;
        }

        return false;
    }

    /**
     * Validate the response
     *
     * @link: https://tools.ietf.org/html/rfc7234#section-4.3.2
     * @return Boolean true if the response is not modified
     */
    public function isNotModified()
    {
        $result  = null;
        $request = $this->getRequest();

        if($this->isCacheable())
        {
            if ($etags = $request->getEtags())
            {
                if(in_array($this->getEtag(), $etags) || in_array('*', $etags)) {
                    $result = true;
                }
            }

            if($since = $request->headers->get('If-Modified-Since') && $this->getLastModified())
            {
                if(!($this->getLastModified()->getTimestamp() > strtotime($since))) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Deep clone of this instance
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();

        $this->_queue  = clone $this->_queue;
    }
}