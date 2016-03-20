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
 * Object Decoratable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object
 */
interface ObjectDecoratable
{
    /**
     * Decorate the object
     *
     * When using decorate(), the object will be decorated by the decorator. The decorator needs to extend from
     * ObjectDecorator.
     *
     * @param   mixed $decorator An ObjectIdentifier, identifier string or object implementing ObjectDecorator
     * @param   array $config  An optional associative array of configuration options
     * @return  ObjectDecorator
     * @throws  ObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws  \UnexpectedValueException If the decorator does not extend from ObjectDecorator
     */
    public function decorate($decorator, $config = array());
}