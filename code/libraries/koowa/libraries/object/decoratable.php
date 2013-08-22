<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Decoratable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectDecoratable
{
    /**
     * Decorate the object
     *
     * When using decorate(), the object will be decorated by the decorator. The decorator needs to extend from
     * ObjectDecorator.
     *
     * @param   mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectDecorator
     * @param   array $config  An optional associative array of configuration options
     * @return  KObjectDecorator
     * @throws  KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws  UnexpectedValueException If the decorator does not extend from KObjectDecorator
     */
    public function decorate($decorator, $config = array());
}