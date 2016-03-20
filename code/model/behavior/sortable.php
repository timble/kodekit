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
 * Sortable Model Behavior
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
            ->insert('sort', 'cmd')
            ->insert('direction', 'word', 'asc');
    }

    /**
     * Split the sort state if format is [column,ASC|DESC]
     *
     * @param   ModelContextInterface $context A model context object
     * @return  void
     */
    protected function _afterReset(ModelContextInterface $context)
    {
        if($context->modified == 'sort' && strpos($context->state->sort, ',') !== false)
        {
            $context->state->sort = explode(',', $context->state->sort);

            foreach($context->state->sort as $key => $value)
            {
                if(strtoupper($value) == 'DESC' || strtoupper($value) == 'ASC')
                {
                    unset($context->state->sort[$key]);
                    $context->state->direction = $value;
                }
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
            $state = $context->state;

            $sort      = trim($state->sort);
            $direction = strtoupper($state->direction);
            $columns   = array_keys($this->getTable()->getColumns());

            if ($sort)
            {
                $column = $this->getTable()->mapColumns($sort);
                $context->query->order($column, $direction);
            }

            if ($sort != 'ordering' && in_array('ordering', $columns)) {
                $context->query->order('tbl.ordering', 'ASC');
            }
        }
    }
}