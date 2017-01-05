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
 * Too Many Requests Http Exception
 *
 * The user has sent too many requests in a given amount of time.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Exception
 */
class HttpExceptionTooManyRequests extends HttpExceptionAbstract
{
    protected $code = HttpResponse::TOO_MANY_REQUESTS;
}
