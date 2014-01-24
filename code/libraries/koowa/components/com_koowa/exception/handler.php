<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Exception Handler
 *
 * Setup error handler for Joomla context.
 *
 * 1. xdebug enabled
 *
 * If xdebug is enabled assume we are in local development mode
 *    - error types   : TYPE_ALL which will trigger an exception for : exceptions, errors and failures
 *    - error levels  : ERROR_DEVELOPMENT (E_ALL | E_STRICT | ~E_DEPRECATED)
 *
 * 2. Joomla debug
 *
 * If debug is enabled assume we are in none local debug mode
 *    - error types   : TYPE_ALL which will trigger an exception for : exceptions, errors and failures
 *    - error levels  : E_ERROR and E_PARSE
 *
 * 3. Joomla default
 *
 * Do not try to trigger errors or exceptions automatically. To trigger an exception the implementing code
 * should call {@link handleException()}
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Exception
 */
class ComKoowaExceptionHandler extends KExceptionHandler
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        if(extension_loaded('xdebug') && xdebug_is_enabled())
        {
            $level = self::ERROR_DEVELOPMENT;
            $type  = self::TYPE_ALL;
        }
        else
        {
            $level = JDEBUG ? E_ERROR | E_PARSE : self::ERROR_REPORTING;
            $type  = JDEBUG ? self::TYPE_ALL : false;
        }

        $config->append(array(
            'exception_type'  => $type,
            'error_level'     => $level
        ));

        parent::_initialize($config);
    }
}