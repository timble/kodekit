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
 * Dispatcher Response Transport Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Dispatcher\Response\Transport
 */
interface DispatcherResponseTransportInterface
{
    /**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

    /**
     * Send the response
     *
     * @param DispatcherResponseInterface $response
     * @return DispatcherResponseTransportInterface
     */
    public function send(DispatcherResponseInterface $response);

    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority();
}