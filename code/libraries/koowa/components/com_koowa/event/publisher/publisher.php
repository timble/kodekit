<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Event Publisher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Event
 */
final class ComKoowaEventPublisher extends KEventPublisher
{
    /**
     * Publish an event by calling all listeners that have registered to receive it.
     *
     * Function will avoid a recursive loop when an exception is thrown during even publishing and output a generic
     * exception instead.
     *
     * @param  Exception           $exception  The exception to be published.
     * @param  array|Traversable    $attributes An associative array or a Traversable object
     * @param  mixed                $target     The event target
     * @return  KEventException
     */
    public function publishException(Exception $exception, $attributes = array(), $target = null)
    {
        //Make sure we have an event object
        $event = new KEventException('onException', $attributes, $target);
        $event->setException($exception);

        parent::publishEvent($event);

        return $event;
    }
}
