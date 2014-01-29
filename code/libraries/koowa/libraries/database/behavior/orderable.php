<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Orderable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KDatabaseBehaviorOrderable extends KDatabaseBehaviorAbstract
{
    /**
     * Get the methods that are available for mixin based
     *
     * This function conditionally mixes the behavior. Only if the mixer has a 'created_by' or 'created_on' property
     * the behavior will be mixed in.
     *
     * @param KObjectMixable $mixer     The mixer requesting the mixable methods.
     * @param  array         $exclude   A list of methods to exclude
     * @return array         An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null, $exclude = array())
	{
		$methods = array();

		if(isset($mixer->ordering)) {
			$methods = parent::getMixableMethods($mixer, $exclude);
		}

		return $methods;
	}
	
	/**
	 * Override to add a custom WHERE clause
	 *
	 * <code>
	 * 	   $query->where('category_id = :category_id')->bind(array('category_id' => $this->id));
	 * </code>
	 *
	 * @param 	KDatabaseQuerySelect $query
	 * @return  void
     *
     * @throws InvalidArgumentException
	 */
	public function _buildQueryWhere($query)
	{
		if(!$query instanceof KDatabaseQuerySelect && !$query instanceof KDatabaseQueryUpdate)
		{
			throw new InvalidArgumentException(
				'Query must be an instance of KDatabaseQuerySelect or KDatabaseQueryUpdate'
			);
		}
	}	

	/**
	 * Move the row up or down in the ordering
	 *
	 * Requires an 'ordering' column
	 *
	 * @param	integer	$change Amount to move up or down
	 * @return 	KDatabaseRowAbstract
	 */
	public function order($change)
	{
		//force to integer
		settype($change, 'int');

		if($change !== 0)
		{
			$old = (int) $this->ordering;
			$new = $this->ordering + $change;
			$new = $new <= 0 ? 1 : $new;

			$table = $this->getTable();
			$query = $this->getObject('koowa:database.query.update')
			    ->table($table->getBase());

			//Build the where query
			$this->_buildQueryWhere($query);

			if($change < 0)
			{
				$query->values('ordering = ordering + 1')
					->where('ordering >= :new')
					->where('ordering <  :old')
					->bind(array('new' => $new, 'old' => $old));
			}
			else
			{
				$query->values('ordering = ordering - 1')
				->where('ordering >  :new')
				->where('ordering <= :old')
				->bind(array('new' => $new, 'old' => $old));
			}

			$table->getAdapter()->update($query);

			$this->ordering = $new;
			$this->save();
			$this->reorder();
		}

		return $this->getMixer();
	}

	 /**
     * Resets the order of all rows
     *
     * Resetting starts at $base to allow creating space in sequence for later record insertion.
     *
     * @param	integer 	$base Order at which to start resetting.
     * @return	KDatabaseBehaviorOrderable
     */
    public function reorder($base = 0)
    {
		//force to integer
        settype($base, 'int');

        $table  = $this->getTable();
        $db     = $table->getDatabase();
        $db->execute('SET @order = '.$base);
        
        $query = $this->getObject('koowa:database.query.update')
            ->table($table->getBase())
            ->values('ordering = (@order := @order + 1)')
            ->order('ordering', 'ASC');

        //Build the where query
        $this->_buildQueryWhere($query);

        if ($base) {
            $query->where('ordering >= :ordering')->bind(array(':ordering' => $base));
        }
        
        $db->update($query);

        return $this;
    }

    /**
     * Find the maximum ordering within this parent
     *
     * @return int
     */
    protected function getMaxOrdering()
    {
        $table  = $this->getTable();
        $db     = $table->getDatabase();
        
        $query = $this->getObject('koowa:database.query.select')
            ->columns('MAX(ordering)')
            ->table($table->getName());

        $this->_buildQueryWhere($query);

        return (int) $db->select($query, KDatabase::FETCH_FIELD);
    }

 	/**
     * Saves the row to the database.
     *
     * This performs an intelligent insert/update and reloads the properties with fresh data from the table on success.
     *
     * @param   KDatabaseContextInterface $context
     * @return KDatabaseRowAbstract
     */
    protected function _beforeInsert(KDatabaseContextInterface $context)
    {
        if(isset($this->ordering))
        {
            if($this->ordering <= 0) {
                $this->ordering = $this->getMaxOrdering() + 1;
            } else {
                $this->reorder($this->ordering);
            } 
        }
    }

    /**
     * Changes the rows ordering if the virtual order field is set. Order is relative to the row's current position.
     *
     * @param   KDatabaseContextInterface $context
     */
    protected function _beforeUpdate(KDatabaseContextInterface $context)
    {
        if(isset($this->order) && isset($this->ordering)) {
            $this->order($this->order);
        }
    }

    /**
     * Clean up the ordering after an item was deleted
     *
     * @param   KDatabaseContextInterface $context
     */
    protected function _afterDelete(KDatabaseContextInterface $context)
    {
        $this->reorder();
    }
}
