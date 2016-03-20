<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Model Context Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Context
 */
interface ModelContextInterface extends CommandInterface
{
    /**
     * Get the model state
     *
     * @return ModelState
     */
    public function getState();

    /**
     * Get the model entity
     *
     * @return ModelEntityInterface
     */
    public function getEntity();

    /**
     * Get the identity key
     *
     * @return mixed
     */
    public function getIdentityKey();
}