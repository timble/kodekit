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
 * Exception Handler
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Exception\Handler
 */
class ExceptionHandlerAbstract extends ObjectAbstract implements ExceptionHandlerInterface
{
    /**
     * The exception callbacks
     *
     * @var array
     */
    private $__exception_callbacks = array();

    /**
     * The exception stack
     *
     * @var array
     */
    private $__exceptions;

    /**
     * The error reporting
     *
     * @var int
     */
    protected $_error_reporting;

    /**
     * If this setting is false, the @ (shut-up) error control operator will be ignored so that notices, warnings and
     * errors are no longer hidden and will fire an ExceptionError.
     *
     * @var bool
     */
    protected $_error_operator;

    /**
     * Exception types
     *
     * @var int
     */
    protected $_exception_type;

    /**
     * Result of error_get_last() cached before the class registers its handlers
     *
     * This is needed to make sure _handleFailure does not handle PHP errors that had happened before
     * the class started handling them
     *
     * @var string
     * @see _handleFailure
     */
    private $__last_unhandled_error;

    /**
     * Flag to determine if the shutdown handler was registered, avoids over-registering
     *
     * @var bool
     * @see enable
     */
    private $__shutdown_registered = false;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        if ($error = error_get_last()) {
            $this->__last_unhandled_error = md5(serialize($error));
        }

        //Set the errors to handle
        $this->setErrorReporting($config->error_reporting);

        //Add handlers
        foreach($config->exception_callbacks as $callback) {
            $this->addExceptionCallback($callback);
        }

        if($config->exception_type) {
            $this->enable($config->exception_type);
        }

        $this->_error_operator = $config->error_operator;

        //Create the exception stack
        $this->__exceptions = $this->getObject('lib:object.stack');
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'exception_callbacks' => array(),
            'exception_type'      => self::TYPE_ALL,
            'error_reporting'     => self::ERROR_REPORTING,
            'error_operator'      => true
        ));

        parent::_initialize($config);
    }

    /**
     * Enable exception handling
     *
     * @param integer $type The type of exceptions to enable
     * @return ExceptionHandler
     */
    public function enable($type = self::TYPE_ALL)
    {
        if($type & self::TYPE_EXCEPTION && !($this->_exception_type & self::TYPE_EXCEPTION))
        {
            set_exception_handler(array($this, '_handleException'));
            $this->_exception_type |= self::TYPE_EXCEPTION;
        }

        if($type & self::TYPE_ERROR && !($this->_exception_type & self::TYPE_ERROR))
        {
            set_error_handler(array($this, '_handleError'));
            $this->_exception_type |= self::TYPE_ERROR;
        }

        if($type & self::TYPE_FAILURE && !($this->_exception_type & self::TYPE_FAILURE))
        {
            if (!$this->__shutdown_registered)
            {
                register_shutdown_function(array($this, '_handleFailure'));
                $this->__shutdown_registered = true;
            }

            $this->_exception_type |= self::TYPE_FAILURE;
        }

        return $this;
    }

    /**
     * Disable exception handling
     *
     * @param integer $type The type of exceptions to disable
     * @return ExceptionHandler
     */
    public function disable($type = self::TYPE_ALL)
    {
        if(($type & self::TYPE_EXCEPTION) && ($this->_exception_type & self::TYPE_EXCEPTION))
        {
            restore_exception_handler();
            $this->_exception_type ^= self::TYPE_EXCEPTION;
        }

        if(($type & self::TYPE_ERROR) && ($this->_exception_type & self::TYPE_ERROR))
        {
            restore_error_handler();
            $this->_exception_type ^= self::TYPE_ERROR;
        }

        if(($type & self::TYPE_FAILURE) && ($this->_exception_type & self::TYPE_FAILURE))
        {
            //Cannot unregister shutdown functions. Check in handler to see if it's enabled.
            $this->_exception_type ^= self::TYPE_FAILURE;
        }

        return $this;
    }

    /**
     * Add an exception callback
     *
     * @param  callable $callback
     * @param  bool $prepend If true, the callback will be prepended instead of appended.
     * @throws \InvalidArgumentException If the callback is not a callable
     * @return ExceptionHandlerAbstract
     */
    public function addExceptionCallback(callable $callback, $prepend = false )
    {
        if($prepend) {
            array_unshift($this->__exception_callbacks, $callback);
        } else {
            array_push($this->__exception_callbacks, $callback);
        }

        return $this;
    }

    /**
     * Remove an exception callback
     *
     * @param  callable $callback
     * @throws \InvalidArgumentException If the callback is not a callable
     * @return ExceptionHandlerAbstract
     */
    public function removeExceptionCallback(callable $callback)
    {
        foreach ($this->__exception_callbacks as $key => $exception_callback)
        {
            if ($exception_callback === $callback)
            {
                unset($this->__exception_callbacks[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Get the registered exception callbacks
     *
     * @return array An array of callables
     */
    public function getExceptionCallbacks()
    {
        return $this->__exception_callbacks;
    }

    /**
     * Get the handled exception stack
     *
     * @return  ObjectStack   An object stack containing the handled exceptions
     */
    public function getExceptions()
    {
        return $this->__exceptions;
    }

    /**
     * Set which PHP errors are handled
     *
     * @param int $level If NULL, will reset the level to the system default.
     */
    public function setErrorReporting($level)
    {
        $this->_error_reporting = null === $level ? error_reporting() : $level;
    }

    /**
     * Get the PHP errors that are being handled
     *
     * @return int The error level
     */
    public function getErrorReporting()
    {
        return $this->_error_reporting;
    }

    /**
     * Handle an exception by calling all callbacks that have registered to receive it.
     *
     * If an exception callback returns TRUE the exception handling will be aborted, otherwise the next callback will be
     * called, until all callbacks have gotten a change to handle the exception.
     *
     * @param  \Exception  $exception  The exception to be handled
     * @return bool  If the exception was handled return TRUE, otherwise false
     */
    public function handleException(\Exception $exception)
    {
        try
        {
            //Try to handle the exception
            foreach($this->getExceptionCallbacks() as $handler)
            {
                if(call_user_func_array($handler, array(&$exception)) === true)
                {
                    $this->__exceptions->push($exception);
                    return true;
                };
            }
        }
        catch (Exception $e)
        {
            $message  = "<p><strong>%s</strong>: '%s' thrown in <strong>%s</strong> on line <strong>%s</strong></p>";
            $message .= "<p>while handling exception</p>";
            $message .= "<p><strong>%s</strong>: '%s' thrown in <strong>%s</strong> on line <strong>%s</strong></p>";
            $message .= "<h3>Stacktrace</h3><pre>%s</pre>";

            $message = sprintf($message,
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );

            //Make sure the output buffers are cleared
            $level = ob_get_level();
            while($level > 0) {
                ob_end_clean();
                $level--;
            }

            if (ini_get('display_errors')) {
                echo $message;
            }

            if (ini_get('log_errors')) {
                error_log($message);
            }

            exit(0);
        }

        return false;
    }

    /**
     * Check if an exception type is enabled
     *
     * @param $type
     * @return bool
     */
    public function isEnabled($type)
    {
        if($this->_exception_type & $type) {
            return true;
        }

        return false;
    }

    /**
     * Exception Handler
     *
     * Do not call this method directly. Function visibility is public because set_exception_handler does not allow for
     * protected method callbacks.
     *
     * @param  object $exception  The exception to be handled
     * @return bool
     */
    public function _handleException($exception)
    {
        $result = false;

        if($this->isEnabled(self::TYPE_EXCEPTION))
        {
            //Handle \Error Exceptions in PHP7
            if (class_exists('Error') && $exception instanceof \Error)
            {
                $message = $exception->getMessage();
                $file    = $exception->getFile();
                $line    = $exception->getLine();
                $type    = E_ERROR; //Set to E_ERROR by default

                if($exception instanceof \DivisionByZeroError) {
                    $type = E_WARNING;
                }

                if($exception instanceof \AssertionError) {
                    $type = E_WARNING;
                }

                if($exception instanceof \ParseError) {
                    $type = E_PARSE;
                }

                if($exception instanceof \TypeError) {
                    $type =  E_RECOVERABLE_ERROR;
                }

                $result = $this->_handleError($type, $message, $file, $line, null, $exception);
            }
            else $result = $this->handleException($exception);
        }

        //Let the normal error flow continue
        return $result;
    }

    /**
     * Error Handler
     *
     * Do not call this method directly. Function visibility is public because set_error_handler does not allow for
     * protected method callbacks.
     *
     * @param int    $level      The level of the error raised
     * @param string $message    The error message
     * @param string $file       The filename that the error was raised in
     * @param int    $line       The line number the error was raised at
     * @param array  $context    An array that points to the active symbol table at the point the error occurred
     * @param object $previous   The previous exception used for the exception chaining
     * @return bool
     */
    public function _handleError($level, $message, $file, $line, array $context = null, $previous = null)
    {
        $result = false;

        if($this->isEnabled(self::TYPE_ERROR))
        {
            $is_suppressed_error = error_reporting() === 0 || (error_reporting() < ($this->getErrorReporting() < 0 ? E_ALL : $this->getErrorReporting()));

            /*
             * Do not handle suppressed errors.
             *
             * error_reporting returns 0 (or something lower than 4437 on PHP8) if the statement causing the error was prepended by the @ error-control operator.
             * @see https://www.php.net/manual/en/language.operators.errorcontrol.php
             * @see https://www.php.net/manual/en/language.operators.errorcontrol.php#125938
             */
            if (!($this->_error_operator && $is_suppressed_error))
            {
                if ($this->getErrorReporting() & $level)
                {
                    $exception = new ExceptionError(
                        $message, HttpResponse::INTERNAL_SERVER_ERROR, $level, $file, $line, $previous
                    );

                    $result = $this->handleException($exception);
                }
            }
            else $result = true;
        }

        //Let the normal error flow continue
        return $result;
    }

    /**
     * Fatal Handler
     *
     * Do not call this method directly. Function visibility is public because register_shutdown_function does not
     * allow for protected method callbacks.
     *
     * @return void
     */
    public function _handleFailure()
    {
        if($this->isEnabled(self::TYPE_FAILURE))
        {
            $error = error_get_last();

            // Make sure error happened after we started handling them
            if ($error && md5(serialize($error)) !== $this->__last_unhandled_error)
            {
                $level = $error['type'];

                if ($this->getErrorReporting() & $level)
                {
                    $exception = new ExceptionFailure(
                        $error['message'], HttpResponse::INTERNAL_SERVER_ERROR, $level, $error['file'], $error['line']
                    );

                    $this->handleException($exception);
                }
            }
        }
    }
}
