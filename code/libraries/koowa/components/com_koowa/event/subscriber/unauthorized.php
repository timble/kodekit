<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Unauthorized Event Subscriber
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
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
                $message = $exception->getMessage();

                if (!$message) {
                    $message = 'You are not authorized to view this resource';
                }

                $translator = $this->getObject('translator');
                $message    = $translator->translate($message);

                if(JFactory::getApplication()->isSite()) {
                    $url = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode((string) $request->getUrl()), false);
                } else {
                    $url = JRoute::_('index.php', false);
                }

                $response->setRedirect($url, $message, 'error');
                $response->addMessage($translator->translate('Please login and try again'), 'notice');
                $response->send();

                $event->stopPropagation();
            }
        }
    }
}