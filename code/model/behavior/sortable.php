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
 * Sortable Model Behavior
 *
 * By making a model sortable you give the flexibility to change how the returned data is sorted to the client.
 * Clients can use the 'sort' URL parameter to control how the returned data is sorted. The sort order for each
 * sort field is ascending unless it is prefixed with a minus (U+002D HYPHEN-MINUS, “-“), in which case it is
 * descending.
 *
 * Example: GET /posts?sort=-created_on,title
 * This means to sort the data by its created time descended and then the title ascended.
 *
 * Based on the Sorting specification in the JSON API
 * @link http://jsonapi.org/format/#fetching-sorting
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Behavior
 */
class ModelBehaviorSortable extends ModelBehaviorAbstract
{
    /**
     * Insert the model states
     *
     * @param ObjectMixable $mixer
     */
    public function onMixin(ObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        $mixer->getState()
            ->insert('sort', 'cmd');
    }

    /**
     * Split the sort state if format is [column,ASC|DESC]
     *
     * @param   ModelContextInterface $context A model context object
     * @return  void
     */
    protected function _afterReset(ModelContextInterface $context)
    {
        if($context->modified->contains('sort'))
        {
            if(is_string($context->state->sort) && strpos($context->state->sort, ',') !== false) {
                $context->state->sort = explode(',', $context->state->sort);
            }
        }
    }

    /**
     * Add order query
     *
     * @param   ModelContextInterface $context A model context object
     * @return  void
     */
    protected function _beforeFetch(ModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof ModelDatabase && !$context->state->isUnique())
        {
            $sort    = (array) ObjectConfig::unbox($context->state->sort);
            $columns = array_keys($this->getTable()->getColumns());

            if ($sort)
            {
                foreach($sort as $column)
                {
                    $direction = substr( $column, 0, 1 ) == '-' ? 'DESC' : 'ASC';
                    $column    = $this->getTable()->mapColumns(ltrim($column, '-'));

                    $context->query->order($column, $direction);
                }
            }

            if (!in_array('ordering', $sort) && in_array('ordering', $columns)) {
                $context->query->order('tbl.ordering', 'ASC');
            }
        }
    }
}