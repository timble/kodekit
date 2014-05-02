<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaDispatcherRequest extends KDispatcherRequest
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
}