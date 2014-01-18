<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Exception Event Publisher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Exception
 */
class KEventPublisherException extends KEventPublisherAbstract
{
    /**
     * Error levels
     */
    const ERROR_SYSTEM       = null;
    const ERROR_DEVELOPMENT  = -1; //E_ALL   | E_STRICT  | ~E_DEPRECATED
    const ERROR_PRODUCTION   = 7;  //E_ERROR | E_WARNING | E_PARSE

    /**
     * The error level.
     *
     * @var int
     */
    protected $_error_level;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the error level
        $this->setErrorLevel($config->error_level);

        if($config->catch_exceptions) {
            $this->catchExceptions();
        }

        if($config->catch_user_errors) {
            $this->catchUserErrors();
        }

        if($config->catch_fatal_errors) {
            $this->catchFatalErrors();
        }
    }

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
            'catch_exceptions'   => true,
            'catch_user_errors'  => false,
            'catch_fatal_errors' => false,
            'error_level'        => self::ERROR_SYSTEM,
        ));

        parent::_initialize($config);
    }

    /**
     * Publish an event by calling all listeners that have registered to receive it.
     *
     * Function will avoid a recursive loop when an exception is thrown during even publishing and output a generic
     * exception instead.
     *
     * @param  KException           $exception  The exception to be published.
     * @param  array|Traversable    $attributes An associative array or a Traversable object
     * @param  mixed                $target     The event target
     * @return  KEventException
     */
    public function publishException(Exception $exception, $attributes = array(), $target = null)
    {
        try
        {
            //Make sure we have an event object
            $event = new KEventException('onException', $attributes, $target);
            $event->setException($exception);

            parent::publishEvent($event);
        }
        catch (Exception $e)
        {
            $message = "<strong>Exception</strong> '%s' thrown while dispatching error: %s in <strong>%s</strong> on line <strong>%s</strong> %s";
            $message = sprintf($message, get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

            if (ini_get('display_errors')) {
                echo $message;
            }

            if (ini_get('log_errors')) {
                error_log($message);
            }

            exit(0);
        }

        return $event;
    }

    /**
     * Set the error level
     *
     * @param int $level If NULL, will reset the level to the system default.
     */
    public function setErrorLevel($level)
    {
        $this->_error_level = null === $level ? error_reporting() : $level;
    }

    /**
     * Get the error level
     *
     * @return int The error level
     */
    public function getErrorLevel()
    {
        return $this->_error_level;
    }

    /**
     * Catch exceptions during runtime
     *
     * @return  string|null Returns the name of the previously defined exception handler, or NULL if no previous handler
     *                      was defined.
     */
    public function catchExceptions()
    {
        $previous = set_exception_handler(array($this, 'handleException'));
        return $previous;
    }

    /**
     * Catch user errors during runtime
     *
     * @return  string|null Returns the name of the previously defined error handler, or NULL if no previous handler
     *                      was defined.
     */
    public function catchUserErrors()
    {
        $previous = set_error_handler(array($this, 'handleUserError'));
        return $previous;
    }

    /**
     * Catch fatal errors after shutdown.
     *
     * @return  void
     */
    public function catchFatalErrors()
    {
        register_shutdown_function(array($this, 'handleUserError'));
    }

    /**
     * Exception Handler
     *
     * @param $exception
     * @return bool
     */
    public function handleException($exception)
    {
        $this->publishException($exception);
        return true;
    }

    /**
     * User Error Handler
     *
     * @param int    $level      The level of the error raised
     * @param string $message    The error message
     * @param string $file       The filename that the error was raised in
     * @param int    $line       The line number the error was raised at
     * @param array  $context    An array that points to the active symbol table at the point the error occurred
     * @return bool
     */
    public function handleUserError($level, $message, $file, $line, $context = null)
    {
        $error_level = $this->getErrorLevel();

        if (0 !== $level)
        {
            if (error_reporting() & $level && $error_level & $level)
            {
                $exception = new KExceptionError($message, KHttpResponse::INTERNAL_SERVER_ERROR, $level, $file, $line);
                $this->publishException($exception, array('context' => $context));
            }

            //Let the normal error flow continue
            return false;
        }

        return false;
    }

    /**
     * Fatal Error Handler
     *
     * @return bool
     */
    public function handleFatalError()
    {
        $error_level = $this->_error_level;

        $error = error_get_last();
        $level = $error['type'];

        if (error_reporting() & $level && $error_level & $level)
        {
            $exception = new KExceptionError($error['message'], KHttpResponse::INTERNAL_SERVER_ERROR, $level, $error['file'], $error['line']);
            $this->publishException($exception);
        }

        return true;
    }
}