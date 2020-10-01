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
 * Cacheable Dispatcher Behavior
 *
 * Handle HTTP caching and validaiton. The caching logic, based on RFC 7234, uses HTTP headers to control caching
 * behavior, cache lifetime and ETag based revalidation.
 *
 * @link https://tools.ietf.org/html/rfc7234
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Dispatcher\Behavior
 */
class DispatcherBehaviorCacheable extends DispatcherBehaviorAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig  $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
            'cache'         => true,
            'cache_private' => false,
            'cache_time'         => 0, //must revalidate
            'cache_time_shared'  => 0, //must revalidate proxy,
            'cache_control'      => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Mixin Notifier
     *
     * This function is called when the mixin is being mixed. It will get the mixer passed in.
     *
     * @param ObjectMixable $mixer The mixer object
     * @return void
     */
    public function onMixin(ObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        if($this->isCacheable())
        {
            //Set cache control default
            $cache_control = (array) KObjectConfig::unbox($this->getConfig()->cache_control);
            $this->getMixer()->getResponse()->getHeaders()->set('Cache-Control', $cache_control);

            //Set max age default
            $this->getMixer()->getResponse()->setMaxAge($this->getConfig()->cache_time, $this->getConfig()->cache_time_shared);
        }
    }

    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        return $this->getConfig() ? parent::isSupported() : false;
    }

    /**
     * Check if the response can be cached
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isCacheable()
    {
        $request = $this->getRequest();

        $cacheable = false;
        if($request->isCacheable() && $this->getConfig()->cache)
        {
            $cacheable = true;

            if(!$this->getConfig()->cache_private && $this->getUser()->isAuthentic()) {
                $cacheable = false;
            }
        }

        return $cacheable;
    }

    /**
     * Send HTTP response
     *
     * Prepares the Response before it is sent to the client. This method set the cache control headers to ensure that
     * it is compliant with RFC 2616 and calculates an etag for the response
     *
     * @link https://tools.ietf.org/html/rfc2616#page-63
     *
     * @param 	DispatcherContextInterface $context The active command context
     */
    protected function _beforeSend(DispatcherContextInterface $context)
    {
        $response = $context->getResponse();
        $request  = $context->getRequest();

        if($this->isCacheable())
        {
            $response->headers->set('Cache-Control', $this->_getCacheControl());

            //Set Validator
            $response->setEtag($this->_getEtag(), !$response->isDownloadable());
        }
    }

    /**
     * Get the cache control directives
     *
     * @link https://tools.ietf.org/html/rfc7234#page-21
     *
     * @return array
     */
    protected function _getCacheControl()
    {
        $response = $this->getResponse();
        $cache    = $response->getCacheControl();

        if($response->getUser()->isAuthentic()) {
            $cache[] = 'private';
        }

        return $cache;
    }

    /**
     * Generate a response etag
     *
     * For files returns a md5 hash of same format as Apache does. Eg "%ino-%size-%0mtime" using the file
     * info, otherwise return a crc32 digest the user identifier and response content
     *
     * @link http://stackoverflow.com/questions/44937/how-do-you-make-an-etag-that-matches-apache
     *
     * @return string
     */
    protected function _getEtag()
    {
        $response = $this->getResponse();

        if($response->isDownloadable())
        {
            $info = $response->getStream()->getInfo();
            $etag = sprintf('"%x-%x-%s"', $info['ino'], $info['size'],base_convert(str_pad($info['mtime'],16,"0"),10,16));
        }
        else $etag = crc32($this->getUser()->getId().'/###'.$this->getResponse()->getContent());

        return $etag;
    }
}