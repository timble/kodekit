<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Exception Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaControllerException extends KControllerView
{
    /**
     * Render an exception
     *
     * @throws InvalidArgumentException If the action parameter is not an instance of KException
     * @param KControllerContextInterface $context	A controller context object
     */
    protected function _actionRender(KControllerContextInterface $context)
    {
        //Check an exception was passed
        if(!isset($context->param) && !$context->param instanceof KException)
        {
            throw new InvalidArgumentException(
                "Action parameter 'exception' [KException] is required"
            );
        }

        //Set the exception data in the view
        $exception = $context->param;

        //If the error code does not correspond to a status message, use 500
        $code = $exception->getCode();
        if(!isset(KHttpResponse::$status_messages[$code])) {
            $code = '500';
        }

        $message = KHttpResponse::$status_messages[$code];
        $traces = $exception->getTrace();

        //Traverse up the trace stack to find the actual function that was not found
        if($traces[0]['function'] == '__call')
        {
            foreach($traces as $trace)
            {
                if($trace['function'] != '__call')
                {
                    $message = "Call to undefined method : ".$trace['class'].$trace['type'].$trace['function'];
                    $file     = isset($trace['file']) ? $trace['file']  : '';
                    $line     = isset($trace['line']) ? $trace['line']  : '';
                    $function = $trace['function'];
                    $class    = $trace['class'];
                    $args     = isset($trace['args'])  ? $trace['args']  : '';
                    $info     = isset($trace['info'])  ? $trace['info']  : '';
                    break;
                }
            }
        }
        else
        {
            $message  = $exception->getMessage();
            $file	  = $exception->getFile();
            $line     = $exception->getLine();
            $function = $traces[0]['function'];
            $class    = isset($traces[0]['class']) ? $traces[0]['class'] : '';
            $args     = isset($traces[0]['args'])  ? $traces[0]['args']  : '';
            $info     = isset($traces[0]['info'])  ? $traces[0]['info']  : '';
        }

        //Create the exception message
        if(ini_get('display_errors')) {
            $message = get_class($exception) ." with message '".$message."' in ".$file.":".$line;
        } else {
            $traces = array();
        }

        $this->getView()->exception = get_class($exception);
        $this->getView()->code      = $code;
        $this->getView()->message   = $message;
        $this->getView()->file      = $file;
        $this->getView()->line      = $line;
        $this->getView()->function  = $function;
        $this->getView()->class     = $class;
        $this->getView()->args      = $args;
        $this->getView()->info      = $info;
        $this->getView()->trace     = $traces;

        //Make sure the buffers are cleared
        while(@ob_get_clean());

        //Render the exception
        $result = parent::_actionRender($context);

        return $result;
    }
}