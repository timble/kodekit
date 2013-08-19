<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Filter Interface
 *
 * Validate or sanitize data
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
interface KFilterInterface extends KCommandInterface, KObjectInstantiatable
{
    /**
     * Validate a value or data collection
     *
     * NOTE: This should always be a simple yes/no question (is $value valid?), so only true or false should be returned
     *
     * @param   mixed   $value Data to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value);

    /**
     * Sanitize a value or data collection
     *
     * @param   mixed   $value Data to be sanitized
     * @return  mixed
     */
    public function sanitize($value);
}
