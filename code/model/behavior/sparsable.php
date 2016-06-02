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
 * Sparsable Model Behavior
 *
 * By making a model sparsable, you enables the ability for clients to choose the returned properties of a model
 * entity with URL query parameters. This is useful for optimizing requests, making API calls more efficient
 * and fast.
 *
 * A client can request to get only specific fields in the response by including a fields[TYPE] parameter. The
 * value of the fields parameter MUST be a comma-separated (U+002C COMMA, “,”) list that refers to the name(s)
 * of the fields to be returned.
 *
 * The behavior will ALAWYS include the identity key property of the specific type in the returned properties.
 *
 * Based on the Sparse Fieldsets specification in the JSON API
 * @link http://jsonapi.org/format/#fetching-sparse-fieldsets
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Behavior
 */
class ModelBehaviorSparsable extends ModelBehaviorAbstract
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
            ->insert('fields', 'cmd', array());
    }

    /**
     * Parse the fields state
     *
     * @param   ModelContextInterface $context A model context object
     * @return  void
     */
    protected function _afterReset(ModelContextInterface $context)
    {
        if($context->modified->contains('fields'))
        {
            $fields = $context->state->fields;

            foreach ($fields as $type => $value)
            {
                if(is_string($value)) {
                    $fields[$type] = array_unique(explode(',', $value));
                }
            }

            $context->state->fields = $fields;
        }
    }

    /**
     * Add query colums based on fields
     *
     * @param   ModelContextInterface $context A model context object
     * @return  void
     */
    protected function _beforeFetch(ModelContextInterface $context)
    {
        $model = $context->getSubject();

        $result = array();
        $columns = $this->getTable()->getColumns(true);

        if ($model instanceof ModelDatabase)
        {
            $fields = $context->state->fields;
            $type   = $model->getIdentifier()->name;

            if(isset($fields[$type]))
            {
                $result  = array();
                $columns = array_keys($this->getTable()->getColumns());

                foreach($fields[$type] as $field)
                {
                    if(in_array($field, $columns))
                    {
                        $column = $this->getTable()->mapColumns($field);
                        $result[] = 'tbl.'.$column;
                    }
                }

                if(!empty($result))
                {
                    $context->query->columns = array();

                    //Always include the identity column
                    $result[] = 'tbl.'.$this->getTable()->getIdentityColumn();
                    $context->query->columns($result);
                }
            }
        }
    }
}