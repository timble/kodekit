<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Toolbar Mixin Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar\Mixin
 */
interface KControllerToolbarMixinInterface
{
    /**
     * Add a toolbar
     *
     * @param   mixed $toolbar An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param  array  $config An optional associative array of configuration settings
     * @throws UnexpectedValueException
     * @return  KObject The mixer object
     */
    public function addToolbar($toolbar, $config = array());

    /**
     * Remove a toolbar
     *
     * @param   KControllerToolbarInterface $toolbar A toolbar instance
     * @return  Object The mixer object
     */
    public function removeToolbar(KControllerToolbarInterface $toolbar);

    /**
     * Check if a toolbar exists
     *
     * @param   string   $type The name of the toolbar
     * @return  boolean  TRUE if the toolbar exists, FALSE otherwise
     */
    public function hasToolbar($type = 'actionbar');

    /**
     * Get a toolbar by type
     *
     * @param  string  $type   The toolbar name
     * @return KControllerToolbarInterface
     */
    public function getToolbar($type = 'actionbar');

    /**
     * Gets the toolbars
     *
     * @return array  An associative array of toolbars, keys are the toolbar names
     */
    public function getToolbars();
}