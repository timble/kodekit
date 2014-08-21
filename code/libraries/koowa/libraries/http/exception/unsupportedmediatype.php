<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Unsupported Media Type Http Exception
 *
 * The server is refusing to service the request because the entity of the request is in a format not supported by the
 * requested resource for the requested method.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http\Exception\Unsupported
 */
class KHttpExceptionUnsupportedMediaType extends KHttpExceptionAbstract
{
    protected $code = KHttpResponse::UNSUPPORTED_MEDIA_TYPE;
}