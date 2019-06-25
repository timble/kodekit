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
 * Abstract Dispatcher Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Dispatcher\Request
 */
abstract class DispatcherRequestAbstract extends ControllerRequest implements DispatcherRequestInterface
{
    /**
     * The request cookies
     *
     * @var HttpMessageParameters
     */
    protected $_cookies;

    /**
     * The request files
     *
     * @var HttpMessageParameters
     */
    protected $_files;

    /**
     * Base url of the request.
     *
     * @var HttpUrl
     */
    protected $_base_url;

    /**
     * Base path of the request.
     *
     * @var string
     */
    protected $_base_path;

    /**
     * Root url of the request.
     *
     * @var HttpUrl
     */
    protected $_root;

    /**
     * Referrer of the request
     *
     * @var HttpUrl
     */
    protected $_referrer;

    /**
     * The supported languages
     *
     * @var array
     */
    protected $_languages;

    /**
     * The supported charsets
     *
     * @var array
     */
    protected $_charsets;

    /**
     * A list of trusted proxies
     *
     * @var array
     */
    protected $_proxies;

    /**
     * The requested ranges
     *
     * @var array
     */
    protected $_ranges;

    /**
     * The transport queue
     *
     * @var	ObjectQueue
     */
    protected $_queue;

    /**
     * List of request transports
     *
     * @var array
     */
    protected $_transports;

    /**
     * The request format
     *
     * @var string
     */
    protected $_format;

    /**
     * Constructor
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Create the transport queue
        $this->_queue = $this->getObject('lib:object.queue');

        //Attach the request transport handlers
        $transports = (array) ObjectConfig::unbox($config->transports);

        foreach ($transports as $key => $value)
        {
            if (is_numeric($key)) {
                $this->attachTransport($value);
            } else {
                $this->attachTransport($key, $value);
            }
        }

        //Set the trusted proxies
        $this->setProxies(ObjectConfig::unbox($config->proxies));

        //Set files parameters
        $this->setFiles($config->files);

        //Set cookie parameters
        $this->setCookies($config->cookies);

        //Set the base URL
        $this->setBaseUrl($config->base_url);

        //Set the base path
        $this->setBasePath($config->base_path);

        //Receive the request
        $this->receive();

        // Set timezone to user's settings
        date_default_timezone_set($this->getTimezone());

        // Set language to user's settings
        locale_set_default($this->getLanguage());
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
            'transports'  => array('server', 'headers', 'data'),
            'base_url'  => '/',
            'base_path' => null,
            'url'       => null,
            'method'   => null,
            'query'   => $_GET,
            'data'    => $_POST,
            'cookies' => $_COOKIE,
            'files'   => $_FILES,
            'proxies' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Receive the request by passing it through transports
     *
     * @return DispatcherRequestTransportInterface
     */
    public function receive()
    {
        foreach($this->_queue as $transport)
        {
            if($transport instanceof DispatcherRequestTransportInterface) {
                $transport->receive($this);
            }
        }

        return $this;
    }

    /**
     * Get a transport handler by identifier
     *
     * @param   mixed $transport An object that implements ObjectInterface, ObjectIdentifier object
     *                                 or valid identifier string
     * @param   array $config An optional associative array of configuration settings
     * @throws \UnexpectedValueException
     * @return DispatcherRequestTransportInterface
     */
    public function getTransport($transport, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($transport) && strpos($transport, '.') === false)
        {
            $identifier = $this->getIdentifier()->toArray();

            if($identifier['package'] != 'dispatcher') {
                $identifier['path'] = array('dispatcher', 'request', 'transport');
            } else {
                $identifier['path'] = array('request', 'transport');
            }

            $identifier['name'] = $transport;
            $identifier = $this->getIdentifier($identifier);
        }
        else $identifier = $this->getIdentifier($transport);

        if (!isset($this->_transports[$identifier->name]))
        {
            $transport = $this->getObject($identifier, array_merge($config, array('request' => $this)));

            if (!($transport instanceof DispatcherRequestTransportInterface))
            {
                throw new \UnexpectedValueException(
                    "Transport handler $identifier does not implement DispatcherRequestTransportInterface"
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
     * @return DispatcherRequestAbstract
     */
    public function attachTransport($transport, $config = array())
    {
        if (!($transport instanceof DispatcherRequestTransportInterface)) {
            $transport = $this->getTransport($transport, $config);
        }

        //Enqueue the transport handler in the command chain
        $this->_queue->enqueue($transport, $transport->getPriority());

        return $this;
    }

    /**
     * Sets a list of trusted proxies.
     *
     * You should only list the reverse proxies that you manage directly.
     *
     * @param array $proxies A list of trusted proxies
     * @return DispatcherRequestInterface
     */
    public function setProxies(array $proxies)
    {
        $this->_proxies = $proxies;
        return $this;
    }

    /**
     * Gets the list of trusted proxies.
     *
     * @return array An array of trusted proxies.
     */
    public function getProxies()
    {
        return $this->_proxies;
    }

    /**
     * Set the request cookies
     *
     * @param  array $parameters
     * @return DispatcherRequestInterface
     */
    public function setCookies($parameters)
    {
        $this->_cookies = $this->getObject('lib:http.message.parameters', array('parameters' => $parameters));
    }

    /**
     * Get the request cookies
     *
     * @return HttpMessageParameters
     */
    public function getCookies()
    {
        return $this->_cookies;
    }

    /**
     * Set the request files
     *
     * @param  array $parameters
     * @return DispatcherRequestInterface
     */
    public function setFiles($parameters)
    {
        $this->_files = $this->getObject('lib:http.message.parameters', array('parameters' => $parameters));
    }

    /**
     * Get the request files
     *
     * @return HttpMessageParameters
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * Returns current request method.
     *
     * @return  string
     */
    public function getMethod()
    {
        if(!isset($this->_method) && isset($_SERVER['REQUEST_METHOD']))
        {
            $method = strtoupper($_SERVER['REQUEST_METHOD']);

            if($method == 'POST')
            {
                if($this->_headers->has('X-Http-Method-Override')) {
                    $method = strtoupper($this->_headers->get('X-Http-Method-Override'));
                }

                if($this->data->has('_method')) {
                    $method = strtoupper($this->data->get('_method', 'alpha'));
                }
            }

            $this->_method = $method;
        }

        return $this->_method;
    }

    /**
     * Sets the request method.
     *
     * @param string $method
     * @return DispatcherRequest
     */
    public function setMethod($method)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        return parent::setMethod($method);
    }

    /**
     * Get the POST or PUT raw content information
     *
     * The raw post data is not available with enctype="multipart/form-data".
     *
     * @return  string  The content data
     */
    public function getContent()
    {
        if (empty($this->_content) && $this->_headers->has('Content-Length') && $this->_headers->get('Content-Length') > 0)
        {
            $data = '';

            $input = fopen('php://input', 'r');
            while ($chunk = fread($input, 1024)) {
                $data .= $chunk;
            }

            fclose($input);

            $this->_content = $data;
        }

        return $this->_content;
    }

    /**
     * Gets the request's scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * Returns the host name.
     *
     * This method can read the client host from the "X-Forwarded-Host" header when the request is proxied and the proxy
     * is trusted. The "X-Forwarded-Host" header must contain the client host name.
     *
     * @link http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.3
     *
     * @throws \UnexpectedValueException when the host name is invalid
     * @return string
     */
    public function getHost()
    {
        if($this->isProxied() && $this->_headers->has('X-Forwarded-Host'))
        {
            $host = $this->_headers->get('X-Forwarded-Host');
            $parts = explode(',', $host);
            $host  = $parts[count($parts) - 1];
        }
        else
        {
            if (!$host = $this->_headers->get('Host'))
            {
                if (!isset($_SERVER['SERVER_NAME'])) {
                    $host = $this->getAddress();
                } else {
                    $host = $_SERVER['SERVER_NAME'];
                }
            }
        }

        // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);

        // Host is lowercase as per RFC 952/2181
        $host = trim(strtolower($host));

        // Make sure host does not contain forbidden characters (see RFC 952 and RFC 2181)
        if ($host && !preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
            throw new \UnexpectedValueException('Invalid Host');
        }

        return $host;
    }

    /**
     * Returns the port on which the request is made.
     *
     * This method can read the client port from the "X-Forwarded-Port" header when the request is proxied and the proxy
     * is trusted. The "X-Forwarded-Port" header must contain the client port.
     *
     * @link http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.5
     *
     * @return string
     */
    public function getPort()
    {
        if ($this->isProxied() && $this->_headers->has('X-Forwarded-Port')) {
            $port = $this->_headers->get('X-Forwarded-Port');
        } else {
            $port = @$_SERVER['SERVER_PORT'];
        }

        return $port;
    }

    /**
     * Return the Url of the request regardless of the server
     *
     * @return  HttpUrl A HttpUrl object
     */
    public function getUrl()
    {
        if(!isset($this->_url))
        {
            //Scheme
            $scheme = $this->getScheme();

            //Host
            $host   = $this->getHost();

            /*
             * Since we are assigning the URI from the server variables, we first need to determine if we
             * are running on apache or IIS.  If PHP_SELF and REQUEST_URI are present, we will assume we
             * are running on apache.
             */
            if (!empty ($_SERVER['PHP_SELF']) && !empty ($_SERVER['REQUEST_URI']))
            {
                //Prepend the protocol, and the http host to the URI string.
                $url = $scheme.'://'.$host . $_SERVER['REQUEST_URI'];
            }
            else
            {
                /*
                 * Since we do not have REQUEST_URI to work with, we will assume we are running on IIS
                 * and will therefore need to work some magic with the SCRIPT_NAME and QUERY_STRING
                 * environment variables.
                 */

                // IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable
                $url = $scheme.'://'.$host . $_SERVER['SCRIPT_NAME'];

                // If the query string exists append it to the URI string
                if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                    $url .= '?' . $_SERVER['QUERY_STRING'];
                }
            }

            // Sanitize the url since we can't trust the server var
            $url = $this->getObject('lib:filter.url')->sanitize($url);

            // Create the URI object
            $this->_url = $this->getObject('lib:http.url', array('url' => $url));

            //Set the url port
            $port = $this->getPort();

            if (($this->_url->scheme == 'http' && $port != 80) || ($this->_url->scheme == 'https' && $port != 443)) {
                $this->_url->port = $port;
            }

            //Set the user
            if($this->_headers->has('PHP_AUTH_USER'))
            {
                $this->_url->user = $this->_headers->get('PHP_AUTH_USER');

                if($this->_headers->has('PHP_AUTH_PW')) {
                    $this->_url->pass = $this->_headers->get('PHP_AUTH_PASS');
                }
            }
        }

        return $this->_url;
    }

    /**
     * Set the url for this request
     *
     * @param string|array  $url Part(s) of an URL in form of a string or associative array like parse_url() returns
     * @return HttpRequestInterface
     */
    public function setUrl($url)
    {
        if(!empty($url)) {
            $this->_url = $this->getObject('lib:http.url', array('url' => $url));
        }

        return $this;
    }

    /**
     * Returns the HTTP referrer.
     *
     * If a base64 encoded _referrer property exists in the request payload, it is used instead of the referrer.
     * 'referer' a commonly used misspelling word for 'referrer'
     * @link http://en.wikipedia.org/wiki/HTTP_referrer
     *
     * @param   boolean  $isInternal Only allow internal url's
     * @return  HttpUrl|null  A HttpUrl object or NULL if no referrer could be found
     */
    public function getReferrer($isInternal = true)
    {
        if(!isset($this->_referrer) && ($this->_headers->has('Referer') || $this->data->has('_referrer')))
        {
            if ($this->data->has('_referrer')) {
                $referrer = base64_decode($this->data->get('_referrer', 'base64'));
            } else {
                $referrer = $this->_headers->get('Referer');
            }

            $this->setReferrer($this->getObject('lib:filter.url')->sanitize($referrer));
        }


        if(isset($this->_referrer) && $isInternal)
        {
            $target_origin = $this->getUrl()->getHost();
            $source_origin = $this->_referrer->getHost();

            // Check if the source matches the target
            if($target_origin !== $source_origin)
            {
                // Special case: check if the source is a subdomain of the target origin
                if ('.'.$target_origin !== substr($source_origin, -1 * (strlen($target_origin)+1))) {
                    return null;
                }
            }
        }

        return $this->_referrer;
    }

    /**
     * Returns the HTTP origin header.
     *
     * @param   boolean  $isInternal Only allow internal URLs
     * @return  HttpUrl|null  A HttpUrl object or NULL if no origin header could be found
     */
    public function getOrigin($isInternal = true)
    {
        $origin = null;

        if ($this->_headers->has('Origin'))
        {
            try {
                $origin = $this->getObject('lib:http.url', [
                    'url' => $this->getObject('lib:filter.url')->sanitize($this->_headers->get('Origin'))
                ]);

                if($isInternal)
                {
                    $target_origin = $this->getUrl()->getHost();
                    $source_origin = $origin->getHost();

                    // Check if the source matches the target
                    if($target_origin !== $source_origin)
                    {
                        // Special case: check if the source is a subdomain of the target origin
                        if ('.'.$target_origin !== substr($source_origin, -1 * (strlen($target_origin)+1))) {
                            $origin = null;
                        }
                    }
                }
            }
            catch (\UnexpectedValueException $e) {}
        }

        return $origin;
    }

    /**
     * Sets the referrer for the request
     *
     * @param  string|HttpUrlInterface $referrer
     * @return $this
     */
    public function setReferrer($referrer)
    {
        if(!($referrer instanceof HttpUrlInterface)) {
            $referrer = $this->getObject('lib:http.url', array('url' => $referrer));
        }

        $this->_referrer = $referrer;

        return $this;
    }

    /**
     * Returns the agent who made the request
     *
     * @return string $_SERVER['HTTP_USER_AGENT'] or an empty string if it's not supplied in the request
     */
    public function getAgent()
    {
        return $this->_headers->get('User-Agent', '');
    }

    /**
     * Returns the client IP address.
     *
     * This method can read the client port from the "X-Forwarded-For" header when the request is proxied and the proxy
     * is trusted. The "X-Forwarded-For" header must contain the client address. The "X-Forwarded-For" header value is a
     * comma+space separated list of IP addresses, the left-most being the original client, and each successive proxy
     * that passed the request adding the IP address where it received the request from.
     *
     * @link http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.2
     *
     * @return string Client IP address or an empty string if it's not supplied in the request
     */
    public function getAddress()
    {
        if($this->isProxied() && $this->_headers->has('X-Forwarded-For'))
        {
            $addresses = $this->_headers->get('X-Forwarded-For');
            $addresses = array_map('trim', explode(',', $addresses));
            $addresses = array_reverse($addresses);

            $address   = $addresses[0];
        }
        else $address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        return $address;
    }

    /**
     * Returns the base URL from which this request is executed.
     *
     * @return  HttpUrl  A HttpUrl object
     */
    public function getBaseUrl()
    {
        if(!$this->_base_url instanceof HttpUrl)
        {
            $base = clone $this->getUrl();
            $base->setUrl(rtrim((string)$this->_base_url, '/'));

            $this->_base_url = $this->getObject('lib:http.url', array('url' => $base->toString(HttpUrl::BASE)));
        }

        return $this->_base_url;
    }

    /**
     * Set the base URL for which the request is executed.
     *
     * @param string $url
     * @return DispatcherRequestInterface
     */
    public function setBaseUrl($url)
    {
        $this->_base_url = $url;
        return $this;
    }

    /**
     * Returns the base path of the request.
     *
     * @param   boolean $fqp If TRUE create a fully qualified path. Default FALSE.
     * @return  string
     */
    public function getBasePath($fqp = false)
    {
        if(!isset($this->_base_path))
        {
            // PHP-CGI on Apache with "cgi.fix_pathinfo = 0". We don't have user-supplied PATH_INFO in PHP_SELF
            if (strpos(PHP_SAPI, 'cgi') !== false && !ini_get('cgi.fix_pathinfo')  && !empty($_SERVER['REQUEST_URI'])) {
                $path = $_SERVER['PHP_SELF'];
            } else {
                $path = $_SERVER['SCRIPT_NAME'];
            }

            $this->_base_path = rtrim(dirname($path), '/\\');
        }

        $path = $fqp ? $_SERVER['DOCUMENT_ROOT'].$this->_base_path : $this->_base_path;

        // Sanitize the path since we can't trust the server var
        return $this->getObject('lib:filter.path')->sanitize($path);

    }

    /**
     * Set the base path for which the request is executed.
     *
     * @param string $path
     * @return DispatcherRequestInterface
     */
    public function setBasePath($path)
    {
        $this->_base_path = $path;
        return $this;
    }

    /**
     * Gets a list of languages acceptable by the client browser.
     *
     * @return array Languages ordered in the user browser preferences
     */
    public function getLanguages()
    {
        if (!isset($this->languages))
        {
            $this->_languages = array();

            if($this->_headers->has('Accept-Language'))
            {
                $accept    = $this->_headers->get('Accept-Language');
                $languages = $this->_parseAccept($accept);

                foreach (array_keys($languages) as $lang)
                {
                    if (strstr($lang, '-'))
                    {
                        $codes = explode('-', $lang);
                        if ($codes[0] == 'i')
                        {
                            // Language not listed in ISO 639 that are not variants
                            // of any listed language, which can be registered with the
                            // i-prefix, such as i-cherokee
                            if (count($codes) > 1) {
                                $lang = $codes[1];
                            }
                        }
                        else
                        {
                            for ($i = 0, $max = count($codes); $i < $max; $i++)
                            {
                                if ($i == 0) {
                                    $lang = strtolower($codes[0]);
                                } else {
                                    $lang .= '_'.strtoupper($codes[$i]);
                                }
                            }
                        }
                    }

                    $this->_languages[] = $lang;
                }
            }
        }

        return $this->_languages;
    }

    /**
     * Returns the request language tag
     *
     * Should return a properly formatted IETF language tag, eg xx-XX
     * @link https://en.wikipedia.org/wiki/IETF_language_tag
     * @link https://tools.ietf.org/html/rfc5646
     *
     * @return string
     */
    public function getLanguage()
    {
        if(!$language = $this->getUser()->getLanguage())
        {
            if ($this->_headers->has('Accept-Language')) {
                $language = locale_accept_from_http($this->_headers->get('Accept-Language'));
            } else {
                $language = $this->getConfig()->language;
            }
        }

        return $language;
    }

    /**
     * Get a list of timezones acceptable by the client
     *
     * @return array|false
     */
    public function getTimezones()
    {
        $country   = locale_get_region($this->getLanguage());
        $timezones = timezone_identifiers_list(\DateTimeZone::PER_COUNTRY, $country);

        return $timezones;
    }

    /**
     * Returns the request timezone
     *
     * This function will return the first timezone it can find based on the country information
     * of the language tag. If the country has multiple timezones the result will not be accurate.
     *
     * @return string
     */
    public function getTimezone()
    {
        if(!$timezone = $this->getUser()->getTimezone())
        {
            if($timezones = $this->getTimezones()) {
                $timezone = $timezones[0];
            } else {
                $timezone = $this->getConfig()->timezone;
            }
        }

        return $timezone;
    }


    /**
     * Gets a list of charsets acceptable by the client browser.
     *
     * @return array List of charsets in preferable order
     */
    public function getCharsets()
    {
        if (!isset($this->_charsets))
        {
            $this->_charsets = array();

            if($this->_headers->has('Accept-Charset'))
            {
                $accept   = $this->_headers->get('Accept-Charset');
                $charsets = $this->_parseAccept($accept);

                $this->_charsets = array_keys($charsets);
            }
        }

        return $this->_charsets;
    }

    /**
     * Gets the request ranges
     *
     * @link : http://tools.ietf.org/html/rfc2616#section-14.35
     *
     * @throws HttpExceptionRangeNotSatisfied If the range info is not valid or if the start offset is large then the end offset
     * @return array List of request ranges
     */
    public function getRanges()
    {
        if(!isset($this->_ranges))
        {
            $this->_ranges = array();

            if($this->_headers->has('Range'))
            {
                $range  = $this->_headers->get('Range');

                if(!preg_match('/^bytes=((\d*-\d*,? ?)+)$/', $range)) {
                    throw new HttpExceptionRangeNotSatisfied('Invalid range');
                }

                $ranges = explode(',', substr($range, 6));
                foreach ($ranges as $key => $range)
                {
                    $parts = explode('-', $range);
                    $first = $parts[0];
                    $last  = $parts[1];

                    $ranges[$key] = array('first' => $first, 'last' => $last);
                }

                $this->_ranges = $ranges;
            }
        }

        return $this->_ranges;
    }

    /**
     * Gets the etags
     *
     * @link https://tools.ietf.org/html/rfc7232#page-14
     *
     * @return array The entity tags
     */
    public function getETags()
    {
        $result = array();
        if($this->_headers->has('If-None-Match'))
        {
            $result = preg_split('/\s*,\s*/', $this->_headers->get('If-None-Match'), null, PREG_SPLIT_NO_EMPTY);

            //Remove the encoding from the etag
            //
            //RFC-7232 explicitly states that ETags should be content-coding aware
            $result = str_replace('-gzip', '', $result);
        }

        return $result;
    }

    /**
     * Checks whether the request is secure or not.
     *
     * This method can read the client scheme from the "X-Forwarded-Proto" header when the request is proxied and the
     * proxy is trusted. The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
     *
     * @link http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.4
     *
     * @return  boolean
     */
    public function isSecure()
    {
        // Some servers are configured to return as X-Forwarded-Proto but they are missing X-Forwarded-By.
        if (/*$this->isProxied() && */$this->_headers->has('X-Forwarded-Proto')) {
            $scheme  = $this->_headers->get('X-Forwarded-Proto');
        } else {
           $scheme  = isset($_SERVER['HTTPS']) ? strtolower($_SERVER['HTTPS']) : 'http';
        }

        return in_array(strtolower($scheme), array('https', 'on', '1'));
    }

    /**
     * Checks whether the request is proxied or not.
     *
     * This method reads the proxy IP from the "X-Forwarded-By" header. The "X-Forwarded-By" header must contain the
     * proxy IP address and, potentially, a port number). If no "X-Forwarded-By" header can be found, or the header
     * IP address doesn't match the list of trusted proxies the function will return false.
     *
     * @link http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#page-7
     *
     * @return  boolean Returns TRUE if the request is proxied and the proxy is trusted. FALSE otherwise.
     */
    public function isProxied()
    {
        if(!empty($this->_proxies) && $this->_headers->has('X-Forwarded-By'))
        {
            $ip      = $this->_headers->get('X-Forwarded-By');
            $proxies = $this->getProxies();

            //Validates the proxied IP-address against the list of trusted proxies.
            foreach ($proxies as $proxy)
            {
                if (strpos($proxy, '/') !== false)
                {
                    list($address, $netmask) = explode('/', $proxy, 2);

                    if ($netmask < 1 || $netmask > 32) {
                        return false;
                    }
                }
                else
                {
                    $address = $proxy;
                    $netmask = 32;
                }

                if(substr_compare(sprintf('%032b', ip2long($ip)), sprintf('%032b', ip2long($address)), 0, $netmask) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the request is downloadable or not.
     *
     * A request is downloading if one of the following conditions are met :
     *
     * 1. The request query contains a 'force-download' parameter
     * 2. The request accepts specifies either the application/force-download or application/octet-stream mime types
     *
     * @return bool Returns TRUE If the request is downloadable. FALSE otherwise.
     */
    public function isDownload()
    {
        $result = $this->query->has('force-download');

        if($this->headers->has('Accept'))
        {
            $accept = $this->headers->get('Accept');
            $types  = $this->_parseAccept($accept);

            //Get the highest quality format
            $type = key($types);

            if(in_array($type, array('application/force-download', 'application/octet-stream'))) {
                return $result = true;
            }
        }

        return $result;
    }

    /**
     * Check if the request is streaming
     *
     * Responses that contain a Range header is considered to be streaming.
     * @link : http://tools.ietf.org/html/rfc2616#section-14.35
     *
     * @return bool
     */
    public function isStreaming()
    {
        return $this->_headers->has('Range');
    }

    /**
     * Implement a virtual 'headers', 'query' and 'data class property to return their respective objects.
     *
     * @param   string $name  The property name.
     * @return  string $value The property value.
     */
    public function __get($name)
    {
        if($name == 'cookies') {
            return $this->getCookies();
        }

        if($name == 'files') {
            return $this->getFiles();
        }

        return parent::__get($name);
    }

    /**
     * Deep clone of this instance
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();

        $this->_cookies = clone $this->_cookies;
        $this->_files   = clone $this->_files;
    }
}
