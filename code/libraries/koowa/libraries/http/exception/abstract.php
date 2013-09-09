<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Http Exception
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http
 */
abstract class KHttpExceptionAbstract extends RuntimeException implements KHttpException
{
    /**
     * Constructor
     *
     * @param string  $message  The exception message
     * @param object  $previous The previous exception
     */
    public function __construct($message = null, Exception $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }
}