<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Dispatcher Response
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher\Response
 */
abstract class KDispatcherResponseAbstract extends KControllerResponse implements KDispatcherResponseInterface
{
    /**
     * Stream resource
     *
     * @var KFilesystemStreamInterface
     */
    private $__stream;

    /**
     * The transport queue
     *
     * @var	KObjectQueue
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
     * @param KObjectConfig $config	An optional ObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create the transport queue
        $this->_queue = $this->getObject('lib:object.queue');

        //Attach the response transport handlers
        $transports = (array) KObjectConfig::unbox($config->transports);

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
     * @param   KObjectConfig $config    An optional ObjectConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'content'     => '',
            'transports'  => array('redirect', 'json', 'http'),
        ));

        parent::_initialize($config);
    }

    /**
     * Send the response
     *
     * Iterate through the response transport handlers. If a handler returns TRUE the chain will be stopped.
     *
     * @return boolean  Returns true if the response has been send, otherwise FALSE
     */
    public function send()
    {
        foreach($this->_queue as $transport)
        {
            if($transport instanceof KDispatcherResponseTransportInterface)
            {
                if($transport->send($this) == true) {
                    return true;
                }
            }
        }

        return false;
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
        if($this->__stream instanceof KFilesystemStreamInterface)
        {
            $this->__stream->truncate(0);
            $this->__stream->write($content);
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
     * @return KFilesystemStreamInterface
     */
    public function getStream()
    {
        if(!isset($this->__stream))
        {
            $content = $this->getContent();
            $factory = $this->getObject('filesystem.stream.factory');

            if(!$this->getObject('filter.path')->validate($content))
            {
                $stream = $factory->createStream('koowa-buffer://memory', 'w+b');
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
     * @param KFilesystemStreamInterface $stream  The stream object
     * @return KDispatcherResponseAbstract
     */
    public function setStream(KFilesystemStreamInterface $stream)
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
     * @throws UnexpectedValueException
     * @return KDispatcherResponseAbstract
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

            if (!($transport instanceof KDispatcherResponseTransportInterface))
            {
                throw new UnexpectedValueException(
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
     * @return KDispatcherResponseAbstract
     */
    public function attachTransport($transport, $config = array())
    {
        if (!($transport instanceof KDispatcherResponseTransportInterface)) {
            $transport = $this->getTransport($transport, $config);
        }

        //Enqueue the transport handler in the command chain
        $this->_queue->enqueue($transport, $transport->getPriority());

        return $this;
    }

    /**
     * Returns true if the response is worth caching under any circumstance.
     *
     * Responses that are streamable are considered un cacheable.
     *
     * @link http://tools.ietf.org/html/rfc2616#section-14.9.1
     * @return Boolean true if the response is worth caching, false otherwise
     */
    public function isCacheable()
    {
        if($this->isStreamable()) {
            return false;
        }

        return parent::isCacheable();
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

        $isPDF        = $this->getContentType() == 'application/pdf';
        $isInline     = !$request->isDownload();
        $isSeekable   = $this->getStream()->isSeekable();

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
     * Check if the response is downloadable
     *
     * @return bool
     */
    public function isDownloadable()
    {
        if($this->getStream()->getType() == 'file') {
            return true;
        }

        return false;
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