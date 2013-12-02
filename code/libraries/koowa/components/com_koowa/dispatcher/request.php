<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Passes Joomla routing results to the request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class ComKoowaDispatcherRequest extends KDispatcherRequest
{
    protected function _initialize(KObjectConfig $config)
    {
        parent::_initialize($config);

        /*
         * Joomla 3.x Compat
         *
         * Re-run the routing and add returned keys to the $_GET request
         * This is done because Joomla 3 sets the results of the router in $_REQUEST and not in $_GET
         */
        $app = JFactory::getApplication();
        if ($app->isSite() && $app->getCfg('sef'))
        {
            $uri = clone JURI::getInstance();

            $router = JFactory::getApplication()->getRouter();
            $result = $router->parse($uri);

            foreach ($result as $key => $value)
            {
                if (!$config->query->has($key)) {
                    $config->query->set($key, $value);
                }
            }
        }

        if ($config->query->limitstart) {
            $config->query->offset = $config->query->limitstart;
        }

        parent::_initialize($config);
    }

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