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
 * Native Session Handler
 *
 * It uses the default registered PHP session handler, whatever that might be
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\User\Session\Handler
 * @link    http://www.php.net/manual/en/function.session-set-save-handler.php
 */
class UserSessionHandlerNative extends UserSessionHandlerAbstract
{
    /**
     * Do nothing since we are going to depend on the current PHP session handler
     */
    public function register()
    {
        static::$_registered = $this;
    }
}