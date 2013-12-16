<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaDispatcherRequestJoomla extends KDispatcherRequest
{
    /**
     * Returns the root URL from which this request is executed.
     *
     * @return KHttpUrl A HttpUrl object
     */
    public function getRootUrl()
    {
        if (!$this->_root_url instanceof KHttpUrl) {
            $this->_root_url = $this->getBaseUrl();
        }

        return $this->_root_url;
    }

    /**
     * Set the root URL for which the request is executed.
     *
     * @param string $url
     * @return KDispatcherRequest
     */
    public function setRootUrl($url)
    {
        $this->_root_url = $url;
        return $this;
    }
}