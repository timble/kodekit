<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Mixes a chain of command behaviour into a class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Mixin
 */
interface KMixinInterface extends KObjectHandlable
{
    /**
     * Get the methods that are available for mixin.
     *
     * @return array An array of methods
     */
    public function getMixableMethods();

	/**
     * Get the mixer object
     *
     * @return object 	The mixer object
     */
    public function getMixer();

    /**
     * Set the mixer object
     *
     * @param object The mixer object
     * @return KMixinInterface
     */
    public function setMixer($mixer);
}