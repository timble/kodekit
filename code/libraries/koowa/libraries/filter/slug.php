<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Slug Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterSlug extends KFilterAbstract implements KFilterTraversable
{
    /**
     * Separator character / string to use for replacing non alphabetic characters in generated slug
     *
     * @var	string
     */
    protected $_separator;

    /**
     * Maximum length the generated slug can have. If this is null the length of the slug column will be used.
     *
     * @var	integer
     */
    protected $_length;

    /**
     * Constructor
     *
     * @param KObjectConfig $config An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_length    = $config->length;
        $this->_separator = $config->separator;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'separator' => '-',
            'length' 	=> 100
        ));

        parent::_initialize($config);
    }

    /**
     * Validate a value
     *
     * Returns true if the string only contains US-ASCII and does not contain
     * any spaces
     *
     * @param   mixed   $value Variable to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        return $this->getObject('lib:filter.cmd')->validate($value);
    }

    /**
     * Sanitize a value
     *
     * Replace all accented UTF-8 characters by unaccented ASCII-7 "equivalents", replace whitespaces by hyphens and
     * lowercase the result.
     *
     * @param   mixed   $value Variable to be sanitized
     * @return  mixed
     */
    public function sanitize($value)
    {
        //remove any '-' from the string they will be used as concatenator
        $value = str_replace($this->_separator, ' ', $value);

        //convert to ascii characters
        $value = $this->getObject('lib:filter.ascii')->sanitize($value);

        //lowercase and trim
        $value = trim(strtolower($value));

        //remove any duplicate whitespace, and ensure all characters are alphanumeric
        $value = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array($this->_separator,''), $value);

        //remove repeated occurrences of the separator
        $value = preg_replace('/['.preg_quote($this->_separator, '/').']+/', $this->_separator, $value);

        //trim separators around the slug
        $value = trim($value, $this->_separator);

        //limit length
        if (strlen($value) > $this->_length) {
            $value = substr($value, 0, $this->_length);
        }

        return $value;
    }
}
