<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Not found Event Subscriber
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaEventSubscriberNotfound extends KEventSubscriberAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => KEvent::PRIORITY_HIGH
        ));

        parent::_initialize($config);
    }

    public function onException(KEventException $event)
    {
        $exception = $event->getException();

        if($exception instanceof KHttpExceptionNotFound && JFactory::getApplication()->isSite())
        {
            $request     = $this->getObject('request');
            $response    = $this->getObject('response');

            if ($request->getFormat() == 'html')
            {
                $url = $request->getReferrer();

                if (!$url) {
                    $url = JURI::base();
                }

                $response->setRedirect($url, $event->getMessage(), KControllerResponse::FLASH_ERROR)
                          ->send();

                //Stop event propagation
                $event->stopPropagation();
            }
            else throw $exception;
        }
    }
}