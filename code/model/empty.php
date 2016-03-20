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
 * Empty Model
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model
 */
final class ModelEmpty extends ModelAbstract
{

    /**
     * Constructor
     *
     * @param  ObjectConfig $config    An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_entity = $this->getObject('lib:model.entity.immutable');
    }

    /**
     * Get the total number of entities
     *
     * @param ModelContext $context A model context object
     * @return string  The output of the view
     */
    protected function _actionCount(ModelContext $context)
    {
        return 0;
    }

    /**
     * Reset the model
     *
     * @param ModelContext $context A model context object
     * @return void
     */
    protected function _actionReset(ModelContext $context)
    {

    }
}