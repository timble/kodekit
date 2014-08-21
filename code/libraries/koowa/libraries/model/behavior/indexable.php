<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Indexable Model Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model\Behavior
 */
class KModelBehaviorIndexable extends KModelBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.fetch', '_buildQuery')
             ->addCommandCallback('before.count', '_buildQuery');
    }

    /**
     * Insert the model states
     *
     * @param KObjectMixable $mixer
     */
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        if ($mixer instanceof KModelDatabase)
        {
            $table = $mixer->getTable();

            // Set the dynamic states based on the unique table keys
            foreach ($table->getUniqueColumns() as $key => $column) {
                $mixer->getState()->insert($key, $column->filter, null, true, $table->mapColumns($column->related, true));
            }
        }
    }

    /**
     * Add order query
     *
     * @param   KModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _buildQuery(KModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof KModelDatabase)
        {
            //Get only the unique states
            $states = $context->state->getValues(true);

            if (!empty($states))
            {
                $columns = array_intersect_key($states, $model->getTable()->getColumns());
                $columns = $model->getTable()->mapColumns($columns);

                foreach ($columns as $column => $value)
                {
                    if (isset($value))
                    {
                        $context->query->where('tbl.' . $column . ' ' . (is_array($value) ? 'IN' : '=') . ' :' . $column)
                            ->bind(array($column => $value));
                    }
                }
            }
        }
    }
}