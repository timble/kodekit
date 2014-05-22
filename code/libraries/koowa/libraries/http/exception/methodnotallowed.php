<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Method Not Allowed Http Exception
 *
 * The request URL does not support the specific request method.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http
 */
class KHttpExceptionMethodNotAllowed extends KHttpExceptionAbstract
{
    protected $code = KHttpResponse::METHOD_NOT_ALLOWED;
}