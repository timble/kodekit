<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
class ComKoowaEventDispatcher extends KEventDispatcher
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'catch_exceptions'   => false,
            'catch_user_errors'  => JDEBUG,
            'catch_fatal_errors' => JDEBUG,
        ));

        parent::_initialize($config);
    }

    /**
     * Dispatches an exception by dispatching arguments to all listeners that handle the event.
     *
     * Function will avoid a recursive loop when an exception is thrown during even dispatching and output a generic
     * exception instead.
     *
     * @link    http://www.php.net/manual/en/function.set-exception-handler.php#88082
     * @param   object|array   $event An array, a KObjectConfig or a KEventException object
     * @return  KEventException
     */
    public function dispatchException($event = array())
    {
        try
        {
            if (!$event instanceof KEventException) {
                $event = new KEventException($event);
            }

            parent::dispatch('onException', $event);
        }
        catch (Exception $exception)
        {
            if (version_compare(JVERSION, '3.0', '>=')) {
                JErrorPage::render($exception);
            } else {
                JError::raiseError($exception->getCode(), $exception->getMessage());
            }
        }

        return $event;
    }
}
