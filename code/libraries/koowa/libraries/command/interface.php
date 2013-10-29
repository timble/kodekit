<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Context Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
interface KCommandInterface
{
    /**
     * Get the command subject
     *
     * @return KObjectInterface The command subject
     */
    public function getSubject();

    /**
     * Set the command subject
     *
     * @param  KObjectInterface $subject The command subject
     * @return KCommandInterface
     */
    public function setSubject(KObjectInterface $subject);
}
