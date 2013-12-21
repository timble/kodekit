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
class KExceptionDispatcherAbstract extends KObject
{
    /**
     * Error modes
     */
    const SYSTEM      = 0;
    const DEVELOPMENT = -1; //E_ALL   | E_STRICT  | ~E_DEPRECATED
    const PRODUCTION  = 7;  //E_ERROR | E_WARNING | E_PARSE

    /**
     * The error level.
     *
     * @var int
     */
    protected $_error_level;

    /**
     * Event dispatcher object
     *
     * @var KEventDispatcherInterface
     */
    protected $_event_dispatcher;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the event dispatcher
        if (is_null($config->event_dispatcher)) {
            throw new InvalidArgumentException('event_dispatcher [KEventDispatcherInterface] config option is required');
        }

        $this->_event_dispatcher = $config->event_dispatcher;

        //Set the error level
        $this->setErrorLevel($config->error_level);

        if($config->catch_user_errors) {
            $this->catchUserErrors();
        }

        if($config->catch_fatal_errors) {
            $this->catchFatalErrors();
        }

        if($config->catch_exceptions) {
            $this->catchExceptions();
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
            'event_dispatcher'   => 'event.dispatcher',
            'catch_exceptions'   => true,
            'catch_user_errors'  => true,
            'catch_fatal_errors' => true,
            'error_level'        => self::SYSTEM,
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
     * @param   object|array   An array, a ObjectConfig or a Event object
     * @return  KExceptionEvent
     */
    public function dispatchException($event = array())
    {
        try
        {
            //Make sure we have an event object
            if (!$event instanceof KExceptionEvent) {
                $event = new KExceptionEvent($event);
            }

            $this->getEventDispatcher()->dispatchEvent('onException', $event);
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
     * Get the event dispatcher
     *
     * @return  KEventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if(!$this->_event_dispatcher instanceof KEventDispatcherInterface)
        {
            $this->_event_dispatcher = $this->getObject($this->_event_dispatcher);

            if(!$this->_event_dispatcher instanceof KEventDispatcherInterface)
            {
                throw new \UnexpectedValueException(
                    'EventDispatcher: '.get_class($this->_event_dispatcher).' does not implement EventDispatcherInterface'
                );
            }
        }

        return $this->_event_dispatcher;
    }

    /**
     * Set the chain of command object
     *
     * @param   KEventDispatcherInterface  $dispatcher An event dispatcher object
     * @return  Object  The mixer object
     */
    public function setEventDispatcher(KEventDispatcherInterface $dispatcher)
    {
        $this->_event_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Exception Handler
     *
     * @param $exception
     * @return bool
     */
    public function handleException($exception)
    {
        $this->dispatchException(array('exception' => $exception));
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
    public function handleUserError($level, $message, $file, $line, $context)
    {
        $error_level = $this->getErrorLevel();

        if (0 !== $level)
        {
            if (error_reporting() & $level && $error_level & $level)
            {
                $exception = new KExceptionError($message, 500, $level, $file, $line);
                $this->dispatchException(array(
                    'exception' => $exception,
                    'context'   => $context
                ));
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
            $exception = new KExceptionError($error['message'], 500, $level, $error['file'], $error['line']);
            $this->dispatchException(array('exception' => $exception));
        }

        return true;
    }
}