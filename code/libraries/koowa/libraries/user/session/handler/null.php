<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * User Null Session Handler
 *
 * Can be used in unit testing or in a situation where persisted sessions are not desired.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User
 * @link    http://www.php.net/manual/en/function.session-set-save-handler.php
 */
class UserSessionHandlerNull extends KUserSessionHandlerAbstract
{

}