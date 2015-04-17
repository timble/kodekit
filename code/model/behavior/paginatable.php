<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Paginatable Model Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model\Behavior
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
     * @return    void
     */
    protected function _beforeFetch(KModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof KModelDatabase && !$context->state->isUnique())
        {
            $state = $context->state;
            $limit = $state->limit;

            if ($limit)
            {
                $offset = $state->offset;
                $total  = $this->count();

                if ($offset !== 0 && $total !== 0)
                {
                    // Recalculate the offset if it is set to the middle of a page.
                    if ($offset % $limit !== 0) {
                        $offset -= ($offset % $limit);
                    }

                    // Recalculate the offset if it is higher than the total
                    if ($offset >= $total) {
                        $offset = floor(($total - 1) / $limit) * $limit;
                    }

                    $state->offset = $offset;
                }

                $context->query->limit($limit, $offset);
            }
        }
    }

    /**
     * Recalculate offset
     *
     * @param   KModelContextInterface $context A model context object
     * @return    void
     */
    protected function _afterReset(KModelContextInterface $context)
    {
        $modified = (array) KObjectConfig::unbox($context->modified);
        if (in_array('limit', $modified))
        {
            $limit = $context->state->limit;

            if ($limit) {
                $context->state->offset = floor($context->state->offset / $limit) * $limit;
            }
        }
    }
}