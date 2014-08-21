<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Unauthorized Event Subscriber
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Event\Subscriber
 */
class ComKoowaEventSubscriberUnauthorized extends KEventSubscriberAbstract
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

        if($exception instanceof KHttpExceptionUnauthorized)
        {
            $request     = $this->getObject('request');
            $response    = $this->getObject('response');

            if ($request->getFormat() == 'html' && $request->isSafe())
            {
                $message = $this->getObject('translator')->translate('You are not authorized to access this resource. Please login and try again.');

                if(JFactory::getApplication()->isSite()) {
                    $url = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode((string) $request->getUrl()), false);
                } else {
                    $url = JRoute::_('index.php', false);
                }

                $response->setRedirect($url, $message, 'error');
                $response->send();

                $event->stopPropagation();
            }
        }
    }
}