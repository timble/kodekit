<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Joomla Dispatcher Response Transport
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class ComKoowaDispatcherResponseTransportJoomla extends KDispatcherResponseTransportAbstract
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config  An optional KObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_HIGHEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Send HTTP response
     *
     * If this is a redirect response, send the response and stop the transport handler chain.
     *
     * @param KDispatcherResponseInterface $response
     * @return boolean
     */
    public function send(KDispatcherResponseInterface $response)
    {
        $request = $response->getRequest();

        if ($request->isGet() && $response->getContentType() === 'text/html'
            && !$response->isRedirect() && $request->query->get('tmpl', 'cmd') != 'koowa')
        {
            $view = $this->getObject('dispatcher')->getController()->getView();

            //Mimetype
            JFactory::getDocument()->setMimeEncoding($view->mimetype);

            //Cookies
            foreach ($response->headers->getCookies() as $cookie)
            {
                setcookie(
                    $cookie->name,
                    $cookie->value,
                    $cookie->expire,
                    $cookie->path,
                    $cookie->domain,
                    $cookie->isSecure(),
                    $cookie->isHttpOnly()
                );
            }

            //Messages
            $messages = $response->getMessages(false);
            foreach($messages as $type => $group)
            {
                if ($type === 'success') {
                    $type = 'message';
                }

                foreach($group as $message) {
                    JFactory::getApplication()->enqueueMessage($message, $type);
                }
            }

            //Content
            echo $response->getContent();

            return true;
        }
    }
}