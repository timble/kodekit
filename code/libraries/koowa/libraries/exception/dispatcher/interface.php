<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Exception Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Exception
 */
interface KExceptionDispatcherInterface
{
    /**
     * Dispatches an exception by dispatching arguments to all listeners that handle the event.
     *
     * Function will avoid a recursive loop when an exception is thrown during even dispatching and output a generic
     * exception instead.
     *
     * @link    http://www.php.net/manual/en/function.set-exception-handler.php#88082
     * @param   object|array   An array, a ObjectConfig or a Event object
     * @return  KExceptionEvent
     */
    public function dispatchException($event = array());

    /**
     * Set the error level
     *
     * @param int $level If NULL, will reset the level to the system default.
     */
    public function setErrorLevel($level);

    /**
     * Get the error level
     *
     * @return int The error level
     */
    public function getErrorLevel($level);

    /**
     * Catch exceptions during runtime
     *
     * @return  string|null Returns the name of the previously defined exception handler, or NULL if no previous handler
     *                      was defined.
     */
    public function catchExceptions();

    /**
     * Catch user errors during runtime
     *
     * @return  string|null Returns the name of the previously defined error handler, or NULL if no previous handler
     *                      was defined.
     */
    public function catchUserErrors();

    /**
     * Catch fatal errors after shutdown.
     *
     * @return  void
     */
    public function catchFatalErrors();

    /**
     * Get the event dispatcher
     *
     * @return  KEventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * Set the chain of command object
     *
     * @param   KEventDispatcherInterface  $dispatcher An event dispatcher object
     * @return  Object  The mixer object
     */
    public function setEventDispatcher(KEventDispatcherInterface $dispatcher);
}