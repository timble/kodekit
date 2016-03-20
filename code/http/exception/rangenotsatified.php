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
 * Method Not Allowed Http Exception
 *
 * The request is out of bounds—that, none of the range values overlap the extent of the resource.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Http\Exception
 * @see http://tools.ietf.org/html/rfc2616#section-10.4.17
 */
class HttpExceptionRangeNotSatisfied extends HttpExceptionAbstract
{
    protected $code = HttpResponse::REQUESTED_RANGE_NOT_SATISFIED;
}