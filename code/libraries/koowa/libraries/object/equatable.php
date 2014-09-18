<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Equatable Interface
 *
 * Used to test if two objects are equal
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectEquatable
{
    /**
     * The equality comparison should neither be done by referential equality nor by comparing object handles
     * (i.e. getHandle() === getHandle()).
     *
     * However, you do not need to compare every object attribute, but only those that are relevant for assessing
     * whether both objects are identical or not.
     *
     * @param KObjectInterface $object
     * @return Boolean
     */
    public function equals(KObjectInterface $object);
}