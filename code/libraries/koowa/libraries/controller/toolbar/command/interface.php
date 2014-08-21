<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Toolbar Command
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar\Command
 */
interface KControllerToolbarCommandInterface extends KControllerToolbarInterface
{
    /**
     * Constructor.
     *
     * @param   string              $name   The command name
     * @param   array|KObjectConfig $config An associative array of configuration settings or a KObjectConfig instance.
     */
    public function __construct( $name, $config = array());

    /**
     * Get the parent node
     *
     * @return	KControllerToolbarCommandInterface
     */
    public function getParent();

    /**
     * Set the parent command
     *
     * @param KControllerToolbarCommandInterface $command The parent command
     * @return KControllerToolbarCommand
     */
    public function setParent(KControllerToolbarCommandInterface $command );

    /**
     * Get the toolbar object
     *
     * @return KControllerToolbarInterface
     */
    public function getToolbar();

    /**
     * Set the parent node
     *
     * @param KControllerToolbarInterface $toolbar The toolbar this command belongs too
     * @return KControllerToolbarCommand
     */
    public function setToolbar(KControllerToolbarInterface $toolbar );
}
