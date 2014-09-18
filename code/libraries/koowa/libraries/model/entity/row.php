<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Model Row Entity
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model\Entity
 */
class KModelEntityRow extends KDatabaseRowAbstract implements KModelEntityInterface
{
    /**
     * Get the entity key
     *
     * @return string
     */
    public function getIdentityKey()
    {
        return parent::getIdentityColumn();
    }
}