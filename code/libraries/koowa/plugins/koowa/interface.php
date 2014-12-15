<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Plugin Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Plugin\Koowa
 */
interface PlgKoowaInterface extends KObjectInterface
{
    /**
     * Connect the plugin to the event dispatcher
     *
     * @param $dispatcher
     */
    public function connect($dispatcher);

    /**
     * Loads the plugin language file
     *
     * @param   string  $extension  The extension for which a language file should be loaded
     * @param   string  $basePath   The basepath to use
     * @return	boolean	True, if the file has successfully loaded.
     */
    public function loadLanguage($extension = '', $basePath = JPATH_BASE);

    /**
     * Get the plugin configuration
     *
     * @return KObjectConfig
     */
    public function getConfig($identifier = null);
}
