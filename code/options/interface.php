<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Options Interface
 *
 * @author  Israel Canasa <https://github.com/raeldc>
 * @package Koowa\Library\Options
 */
interface KOptionsInterface extends KObjectConfigInterface
{
    /**
     * Gets the options identifier.
     *
     * @return	string
     */
    public function getIdentifier();

    /**
     * Gets the Provider that instantiated this Options
     *
     * @return	string
     */
    public function getProvider();

    /**
     * Store the Options using the Provider
     *
     * @return	boolean Whether store was successful
     */
    public function store();
}
