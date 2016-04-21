<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Controller Toolbar Command
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller\Toolbar\Command
 */
interface ControllerToolbarCommandInterface extends ControllerToolbarInterface
{
    /**
     * Constructor.
     *
     * @param	string $name The command name
     * @param   array|ObjectConfig 	An associative array of configuration settings or a ObjectConfig instance.
     */
    public function __construct( $name, $config = array());

    /**
     * Get the command label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Check if the commmand is allowed
     *
     * @return bool
     */
    public function isAllowed();

    /**
     * Check if the commmand is disabled
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Get the toolbar object
     *
     * @return ControllerToolbarInterface
     */
    public function getToolbar();

    /**
     * Set the parent node
     *
     * @param ControllerToolbarInterface $toolbar The toolbar this command belongs too
     * @return ControllerToolbarCommand
     */
    public function setToolbar(ControllerToolbarInterface $toolbar );
}
