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
 * User Null Session Handler
 *
 * Can be used in unit testing or in a situation where persisted sessions are not desired.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\User\Session\Handler
 * @link    http://www.php.net/manual/en/function.session-set-save-handler.php
 */
class UserSessionHandlerNull extends UserSessionHandlerAbstract {}