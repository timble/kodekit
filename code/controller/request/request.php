<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Request
 */
class KControllerRequest extends KHttpRequest implements KControllerRequestInterface
{
    /**
     * The request query
     *
     * @var KHttpMessageParameters
     */
    protected $_query;

    /**
     * The request data
     *
     * @var KHttpMessageParameters
     */
    protected $_data;

    /**
     * The request format
     *
     * @var string
     */
    protected $_format;

    /**
     * User object
     *
     * @var	string|object
     */
    protected $_user;

    /**
     * Constructor
     *
     * @param KObjectConfig|null $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Set the user identifier
        $this->_user = $config->user;

        //Set query parameters
        $this->setQuery($config->query);

        //Set data parameters
        $this->setData($config->data);

        //Set the format
        $this->setFormat($config->format);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'query'    => array(),
            'data'     => array(),
            'format'   => 'html',
            'user'     => array(),
            'language' => locale_get_default(),
            'timezone' => date_default_timezone_get(),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the Url of the request regardless of the server
     *
     * @return  KHttpUrl A HttpUrl object
     */
    public function getUrl()
    {
        $url = parent::getUrl();

        //Add the query to the URL
        $url->setQuery($this->getQuery()->toArray());

        return $url;
    }

    /**
     * Set the request query
     *
     * @param  array $parameters
     * @return KControllerRequest
     */
    public function setQuery($parameters)
    {
        $this->_query = $this->getObject('lib:http.message.parameters', array('parameters' => $parameters));
        return $this;
    }

    /**
     * Get the request query
     *
     * @return KHttpMessageParameters
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Set the request data
     *
     * @param  array $parameters
     * @return KControllerRequest
     */
    public function setData($parameters)
    {
        $this->_data = $this->getObject('lib:http.message.parameters', array('parameters' => $parameters));
        return $this;
    }

    /**
     * Get the request query
     *
     * @return KHttpMessageParameters
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Return the request format
     *
     * @return  string  The request format
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Set the request format
     *
     * @param $format
     * @return KControllerRequest
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Set the user object
     *
     * @param KUserInterface $user A request object
     * @return KControllerRequest
     */
    public function setUser(KUserInterface $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * Get the user object
     *
     * @throws UnexpectedValueException	If the user doesn't implement the KUserInterface
     * @return KUserInterface
     */
    public function getUser()
    {
        if(!$this->_user instanceof KUserInterface)
        {
            $this->_user = $this->getObject('user', KObjectConfig::unbox($this->_user));

            if(!$this->_user instanceof KUserInterface)
            {
                throw new UnexpectedValueException(
                    'User: '.get_class($this->_user).' does not implement KUserInterface'
                );
            }
        }

        return $this->_user;
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
        if(!$language = $this->getUser()->getLanguage()) {
            $language = $this->getConfig()->language;
        }

        return $language;
    }

    /**
     * Returns the request timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        if(!$timezone = $this->getUser()->getLanguage()) {
            $timezone = $this->getConfig()->timezone;
        }

        return $timezone;
    }

    /**
     * Implement a virtual 'headers', 'query' and 'data class property to return their respective objects.
     *
     * @param   string $name  The property name.
     * @return  mixed The property value.
     */
    public function __get($name)
    {
        $result = null;
        if($name == 'headers') {
            $result = $this->getHeaders();
        }

        if($name == 'query') {
            $result = $this->getQuery();
        }

        if($name == 'data') {
            $result =  $this->getData();
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

        $this->_data  = clone $this->_data;
        $this->_query = clone $this->_query;
        $this->_user  = clone $this->_user;
    }
}