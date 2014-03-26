<?php
/**
 * Orderable Model Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package NookuLibraryOrderable
 */
class KModelBehaviorOrderable extends KModelBehaviorAbstract
{
    /**
     * Insert the model states
     *
     * @param KObjectMixable $mixer
     */
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        $mixer->getState()
            ->insert('sort', 'cmd')
            ->insert('direction', 'word', 'asc');
    }

    /**
     * Add order query
     *
     * @param   KModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _beforeFetch(KModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof KModelDatabase && !$context->state->isUnique()) {
            $state = $context->state;

            $sort      = $state->sort;
            $direction = strtoupper($state->direction);
            $columns   = array_keys($this->getTable()->getColumns());

            if ($sort) {
                $column = $this->getTable()->mapColumns($sort);

                //if(in_array($column, $columns)) {
                $context->query->order($column, $direction);
                //}
            }

            if ($sort != 'ordering' && in_array('ordering', $columns)) {
                $context->query->order('tbl.ordering', 'ASC');
            }
        }
    }
}