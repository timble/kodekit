<?php
/**
 * Searchable Model Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package NookuLibrarySearchable
 */
class KModelBehaviorSearchable extends KModelBehaviorAbstract
{
    /**
     * The column names to search in
     *
     * Default is 'title'.
     *
     * @var array
     */
    protected $_columns;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_columns = (array)KObjectConfig::unbox($config->columns);

        $this->addCommandCallback('before.fetch', '_buildQuery')
            ->addCommandCallback('before.count', '_buildQuery');
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     *
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'columns' => 'title',
        ));

        parent::_initialize($config);
    }

    /**
     * Insert the model states
     *
     * @param KObjectMixable $mixer
     */
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        $mixer->getState()
            ->insert('search', 'string');
    }

    /**
     * Add search query
     *
     * @param   KModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _buildQuery(KModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof KModelDatabase && !$context->state->isUnique()) {
            $state  = $context->state;
            $search = $state->search;

            if ($search) {
                $columns = array_keys($this->getTable()->getColumns());

                foreach ($this->_columns as $column) {
                    if (in_array($column, $columns)) {
                        $context->query->where('(tbl.' . $column . ' LIKE :search)', 'OR')->bind(array('search' => '%' . $search . '%'));
                    }
                }
            }
        }
    }
}