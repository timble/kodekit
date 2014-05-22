<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Model Rowset Entity
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model
 */
class KModelEntityRowset extends KDatabaseRowsetAbstract implements KModelEntityInterface
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