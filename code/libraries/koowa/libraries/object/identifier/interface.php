<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Identifier Interface
 *
 * Wraps identifiers of the form [application::]type.component.[.path].name in an object, providing public accessors
 * and methods for derived formats
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectIdentifierInterface extends Serializable
{
    /**
     * Formats the identifier as a [application::]type.component.[.path].name string
     *
     * @return string
     */
    public function __toString();
}
