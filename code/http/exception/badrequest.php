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
 * Bead Request Http Exception
 *
 * The request itself or the data supplied along with the request is invalid and could not be processed by the server.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Exception
 */
class HttpExceptionBadRequest extends HttpExceptionAbstract
{
    protected $code = HttpResponse::BAD_REQUEST;
}