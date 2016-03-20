<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Not Implemented Http Exception
 *
 * The server does not support the functionality required to fulfill the request.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Exception
 */
class HttpExceptionNotImplemented extends HttpExceptionAbstract
{
    protected $code = HttpResponse::NOT_IMPLEMENTED;
}