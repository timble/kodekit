<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Exception Event
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
class KEventException extends KEvent implements KException
{
    /**
     * Set the exception
     *
     * @param Exception $exception
     */
    public function setException(Exception $exception)
    {
        return KObjectConfig::set('exception', $exception);
    }

    /**
     * Get the exception
     *
     * @return Exception
     */
    public function getException()
    {
        return KObjectConfig::get('exception');
    }

    /**
     * Return the error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->exception->getMessage();
    }

    /**
     * Return the error code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->exception->getCode();
    }

    /**
     * Return the source filename
     *
     * @return string
     */
    public function getFile()
    {
        return $this->exception->getFile();
    }

    /**
     * Return the source line number
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->exception->getLine();
    }

    /**
     * Return the backtrace information
     *
     * @return array
     */
    public function getTrace()
    {
        return $this->exception->getTrace();
    }

    /**
     * Return the backtrace as a string
     *
     * @return string
     */
    public function getTraceAsString()
    {
        return $this->exception->getTraceAsString();
    }

    /**
     * Format the error for display
     *
     * @return string
     */
    public function toString()
    {
        return (string) $this->exception;
    }

    /**
     * Cast the object to a string
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->toString();
    }
}