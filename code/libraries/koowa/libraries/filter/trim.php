<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Trim Filter.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterTrim extends KFilterAbstract
{
	/**
     * List of characters provided to the trim() function
     *
     * If this is null, then trim() is called with no specific character list, and its default behavior will be invoked,
     * trimming whitespace.
     *
     * @var string|null
     */
    protected $_charList = null;

    /**
     * Constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // List of user-defined tags
        if(isset($config->char_list)) {
            $this->_charList = $config->char_list;
        }
    }

    /**
     * Returns the charList option
     *
     * @return string|null
     */
    public function getCharList()
    {
        return $this->_charList;
    }

    /**
     * Sets the charList option
     *
     * @param  string|null $charList
     * @return KFilterTrim
     */
    public function setCharList($charList)
    {
        $this->_charList = $charList;
        return $this;
    }

    /**
     * Validate a value
     *
     * @param   scalar  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return (is_string($value));
    }

    /**
     * Sanitize a value
     *
     * Returns the variable with characters stripped from the beginning and end
     *
     * @param   mixed   $value Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        if (null === $this->_charList) {
            return trim((string) $value);
        } else {
            return trim((string) $value, $this->_charList);
        }
    }
}
