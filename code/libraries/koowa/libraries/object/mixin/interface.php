<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Mixin Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Mixin
 */
interface KObjectMixinInterface extends KObjectHandlable
{
	/**
     * Get the mixer object
     *
     * @return KObject The mixer object
     */
    public function getMixer();

    /**
     * Set the mixer object
     *
     * @param  KObjectMixable $mixer The mixer object
     * @return KObjectMixinInterface
     */
    public function setMixer(KObjectMixable $mixer);

    /**
     * Mixin Notifier
     *
     * This function is called when the mixin is being mixed. It will get the mixer passed in.
     *
     * @param KObjectMixable $mixer The mixer object
     * @return void
     */
    public function onMixin(KObjectMixable $mixer);

    /**
     * Get a list of all the available methods
     *
     * @return array An array
     */
    public function getMethods();

    /**
     * Get the methods that are available for mixin.
     *
     * Only public methods can be mixed
     *
     * @param KObjectMixable $mixer The mixer requesting the mixable methods.
     * @return array An array of public methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null);
}