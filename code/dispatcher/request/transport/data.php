<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * JSON Dispatcher Request Transport Header
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Dispatcher\Request\Transport
 */
class KDispatcherRequestTransportData extends KDispatcherRequestTransportAbstract
{
    /**
     * Receive request
     *
     * @param KDispatcherRequestInterface $request
     */
    public function receive(KDispatcherRequestInterface $request)
    {
        //Set request data
        if($request->getContentType() == 'application/x-www-form-urlencoded')
        {
            if (in_array($request->getMethod(), array('PUT', 'DELETE', 'PATCH')))
            {
                parse_str($request->getContent(), $data);
                $request->getData()->add($data);
            }
        }

        if($request->getFormat() === 'json')
        {
            if(in_array($request->getMethod(), array('POST', 'PUT', 'DELETE', 'PATCH')))
            {
                $data = array();

                if ($content = $request->getContent()) {
                    $data = json_decode($content, true);
                }

                $request->getData()->add($data);
            }
        }
    }
}