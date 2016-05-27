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
 * Model Context Database
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Context
 */
class ModelContextDatabase extends ModelContext
{
    /**
     * Set the model query
     *
     * @param DatabaseQueryInterface $query
     * @return ModelContext
     */
    public function setQuery($query)
    {
        return ObjectConfig::set('query', $query);
    }

    /**
     * Get the model query
     *
     * @return DatabaseQueryInterface
     */
    public function getQuery()
    {
        return ObjectConfig::get('query');
    }
}