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
 * Event Subscriber Factory
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Event\Subscriber
 */
class EventSubscriberException extends EventSubscriberAbstract
{
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'priority' => Event::PRIORITY_LOW
        ));

        parent::_initialize($config);
    }

    /**
     * Render an exception
     *
     * @throws \InvalidArgumentException If the action parameter is not an instance of Exception
     * @param EventInterface $event Exception event
     */
    public function onException(EventInterface $event)
    {
        $response  = $this->getObject('response');

        $exception = $event->exception;

        //Make sure the output buffers are cleared
        $level = ob_get_level();
        while($level > 0) {
            ob_end_clean();
            $level--;
        }

        //If the error code does not correspond to a status message, use 500
        $code = $exception->getCode();
        if(!isset(HttpResponse::$status_messages[$code])) {
            $code = '500';
        }

        //Get the error message
        $message = HttpResponse::$status_messages[$code];

        //Set the response status
        $response->setStatus($code , $message);

        //Send the response
        $response->send();
    }
}