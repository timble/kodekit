<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Options Provider Interface
 *
 * @author  Israel Canasa <https://github.com/raeldc>
 * @package Koowa\Library\Options
 */
interface KOptionsProviderInterface
{
    /**
     * Loads the options for the given identifier
     *
     * @param string    $identifier     A unique options identifier
     * @param bool      $refresh        If TRUE and the option has already been loaded it will be re-loaded.
     * @return KOptionsInterface  Returns a KoptionInterface object
     */
    public function load($identifier, $refresh = false);

    /**
     * Fetch the options for the given option identifier from the backend
     *
     * @param string    $identifier    A unique options identifier
     * @return KOptionsInterface|null   Returns a OptionInterface object or NULL if the options could not be found.
     */
    public function fetch($identifier);

    /**
     * Create a options object
     *
     * @param array     $data   An associative array of option data
     * @return KOptionsInterface    Returns a KOptionsInterface object
     */
    public function create($identifier, $data = array());

    /**
     * Store a options object in the provider
     *
     * @param string    $identifier     A unique options identifier
     * @param array     $data           An associative array of option data
     * @return boolean  Whether store was successful
     */
    public function store($identifier, $data);

    /**
     * Check if a options has already been loaded for a given options identifier
     *
     * @param string    $identifier     A unique option identifier
     * @return boolean  TRUE if a option has already been loaded. FALSE otherwise
     */
    public function isLoaded($identifier);
}
