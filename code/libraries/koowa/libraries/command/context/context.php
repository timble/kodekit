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
class KCommandContext extends KObjectConfig implements KCommandContextInterface
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
    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * Get the error
     *
     * @return  string|Exception  The error
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Set a command property
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $this->_data[$name] = new KObjectConfig($value);
        } else {
            $this->_data[$name] = $value;
        }
    }
}
