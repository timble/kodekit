<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Model Context Interface
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Model
 */
interface KModelContextInterface extends KCommandInterface
{
    /**
     * Get the model state
     *
     * @return KModelState
     */
    public function getState();

    /**
     * Get the model entity
     *
     * @return KModelEntityInterface
     */
    public function getEntity();

    /**
     * Get the identity key
     *
     * @return mixed
     */
    public function getIdentityKey();
}