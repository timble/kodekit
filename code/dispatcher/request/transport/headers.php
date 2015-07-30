<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * JSON Dispatcher Request Transport Header
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Dispatcher\Request\Transport
 */
class KDispatcherRequestTransportHeaders extends KDispatcherRequestTransportAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Receive request
     *
     * @param KDispatcherRequestInterface $request
     */
    public function receive(KDispatcherRequestInterface $request)
    {
        //Set the headers
        $headers = array();
        foreach ($_SERVER as $key => $value)
        {
            if ($value && strpos($key, 'HTTP_') === 0)
            {
                // Cookies are handled using the $_COOKIE superglobal
                if (strpos($key, 'HTTP_COOKIE') === 0) {
                    continue;
                }

                $headers[substr($key, 5)] = $value;
            }
            elseif ($value && strpos($key, 'CONTENT_') === 0)
            {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name == 'MD5') ? $name : ucfirst(strtolower($name)));

                $headers[$name] = $value;
            }
        }

        $request->getHeaders()->add($headers);
    }
}