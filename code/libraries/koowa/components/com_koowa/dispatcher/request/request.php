<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Dispatcher Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Dispatcher\Request
 */
final class ComKoowaDispatcherRequest extends KDispatcherRequest
{
    /**
     * Returns the site URL from which this request is executed.
     *
     * @return  KHttpUrl  A HttpUrl object
     */
    public function getSiteUrl()
    {
        $url = clone $this->getBaseUrl();

        if(JFactory::getApplication()->getName() == 'administrator')
        {
            // Replace the application name only once since it's possible that
            // we can run from http://localhost/administrator/administrator
            $i    = 1;
            $path = str_ireplace('/administrator', '', $url->getPath(), $i);
            $url->setPath($path);
        }

        return $url;
    }

    /**
     * Forces format to "rss" if it comes in as "feed" per Joomla conventions if SEF suffixes are enabled
     *
     * {@inheritdoc}
     */
    public function getFormat()
    {
        $format = parent::getFormat();

        if (JFactory::getApplication()->getCfg('sef_suffix') && $format === 'feed') {
            $format = 'rss';
        }

        return $format;
    }

    /**
     * If PHP is on a secure connection always return 443 instead of 80
     *
     * When PHP is behind a reverse proxy port information might not be forwarded correctly.
     * Also, $_SERVER['SERVER_PORT'] is not configured correctly on some hosts and always returns 80.
     *
     * {@inheritdoc}
     */
    public function getPort()
    {
        $port = parent::getPort();

        if ($this->isSecure() && $port == '80') {
            $port = '443';
        }

        return $port;
    }
}