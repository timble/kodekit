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
class KDispatcherRequestTransportJsonapi extends KDispatcherRequestTransportAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
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
        if($request->getContentType() == 'application/vnd.api+json' && !$request->isSafe())
        {
            if (is_array($request->data->data))
            {
                $data = $request->data->data;

                if (isset($data['attributes']) && is_array($data['attributes'])) {
                    $request->data->add($data['attributes']);
                }

                var_dump($request->data->toArray(), $request->query->toArray());die;
            }
        }
    }
}