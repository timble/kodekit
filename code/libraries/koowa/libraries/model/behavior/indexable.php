<?php
/**
 * Indexable Model Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package NookuLibraryController
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

        if ($mixer instanceof KModelDatabase) {
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

        if ($model instanceof KModelDatabase) {
            //Get only the unique states
            $states = $context->state->getValues(true);

            if (!empty($states)) {
                $states = $model->getTable()->mapColumns($states);
                foreach ($states as $key => $value) {
                    if (isset($value)) {
                        $context->query->where('tbl.' . $key . ' ' . (is_array($value) ? 'IN' : '=') . ' :' . $key)
                            ->bind(array($key => $value));
                    }
                }
            }
        }
    }
}