<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Unauthorized Http Exception
 *
 * The request requires user authentication
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http
 */
class KHttpExceptionUnauthorized extends KHttpExceptionAbstract
{
    protected $code = KHttpResponse::UNAUTHORIZED;
}