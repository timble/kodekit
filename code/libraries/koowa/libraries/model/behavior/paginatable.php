<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Paginatable Model Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Model
 */
class KModelBehaviorPaginatable extends KModelBehaviorAbstract
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
            ->insert('limit', 'int')
            ->insert('offset', 'int');
    }

    /**
     * Get the model paginator object
     *
     * @return  KModelPaginator  The model paginator object
     */
    public function getPaginator()
    {
        $paginator = new KModelPaginator(array(
            'offset' => (int)$this->getState()->offset,
            'limit'  => (int)$this->getState()->limit,
            'total'  => (int)$this->count(),
        ));

        return $paginator;
    }

    /**
     * Add limit query
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
            $limit = $state->limit;

            if ($limit) {
                $offset = $state->offset;
                $total  = $this->count();

                //If the offset is higher than the total recalculate the offset
                if ($offset !== 0 && $total !== 0) {
                    if ($offset >= $total) {
                        $offset        = floor(($total - 1) / $limit) * $limit;
                        $state->offset = $offset;
                    }
                }

                $context->query->limit($limit, $offset);
            }
        }
    }

    /**
     * Recalculate offset
     *
     * @param   KModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _afterReset(KModelContextInterface $context)
    {
        $limit                  = $context->state->limit;
        $context->state->offset = $limit != 0 ? (floor($context->state->offset / $limit) * $limit) : 0;
    }
}