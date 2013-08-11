<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
class KCommandContext extends KConfig
{
    /**
     * Error
     *
     * @var string
     */
    protected $_error;

    /**
     * Set the error
     *
     * @param string $error
     *
     * @return  KCommandContext
     */
    function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * Get the error
     *
     * @return  string|Exception  The error
     */
    function getError()
    {
        return $this->_error;
    }
}
