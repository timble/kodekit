<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Lockable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KDatabaseBehaviorLockable extends KDatabaseBehaviorAbstract
{
	/**
	 * The lock lifetime
	 *
	 * @var integer
	 */
	protected $_lifetime;

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
	protected function _initialize(KObjectConfig $config)
    {
    	$config->append(array(
			'priority'   => self::PRIORITY_HIGH,
    	    'lifetime'	 => '900' //in seconds
	  	));

	  	$this->_lifetime = $config->lifetime;

    	parent::_initialize($config);
   	}

    /**
     * Get the methods that are available for mixin based
     *
     * This function conditionally mixes the behavior. Only if the mixer has a 'created_by' or 'created_on' property
     * the behavior will be mixed in.
     *
     * @param KObjectMixable $mixer The mixer requesting the mixable methods.
     * @return array         An array of methods
     */
	public function getMixableMethods(KObjectMixable $mixer = null)
	{
		$methods = array();

		if(isset($mixer->locked_by) && isset($mixer->locked_on)) {
			$methods = parent::getMixableMethods($mixer);
		}

		return $methods;
	}

	/**
	 * Lock a row
	 *
	 * Requires an 'locked_on' and 'locked_by' column
	 *
	 * @return boolean	If successful return TRUE, otherwise FALSE
	 */
	public function lock()
	{
		//Prevent lock take over, only an saved and unlocked row and be locked
		if(!$this->isNew() && !$this->locked())
		{
			$this->locked_by = (int) JFactory::getUser()->get('id');
			$this->locked_on = gmdate('Y-m-d H:i:s');
			$this->save();
		}

		return true;
	}

	/**
	 * Unlock a row
	 *
	 * Requires an locked_on and locked_by column to be present in the table
	 *
	 * @return boolean	If successful return TRUE, otherwise FALSE
	 */
	public function unlock()
	{
		$userid = JFactory::getUser()->get('id');

		//Only an saved row can be unlocked by the user who locked it
		if(!$this->isNew() && $this->locked_by != 0 && $this->locked_by == $userid)
		{
			$this->locked_by = 0;
			$this->locked_on = 0;

			$this->save();
		}

		return true;
	}

	/**
	 * Checks if a row is locked
	 *
	 * @return boolean	If the row is locked TRUE, otherwise FALSE
	 */
	public function locked()
	{
		$result = false;
		if(!$this->isNew())
		{
		    if(isset($this->locked_on) && isset($this->locked_by))
			{
			    $locked  = strtotime($this->locked_on);
                $current = strtotime(gmdate('Y-m-d H:i:s'));

                //Check if the lock has gone stale
                if($current - $locked < $this->_lifetime)
			    {
                    $userid = JFactory::getUser()->get('id');
			        if($this->locked_by != 0 && $this->locked_by != $userid) {
			            $result= true;
                    }
			    }
			}
		}

		return $result;
	}

	/**
	 * Checks if a row can be updated
	 *
	 * This function determines if a row can be updated based on it's locked_by information. If a row is locked, and
     * not by the logged in user, the function will return false, otherwise it will return true
	 *
     * @param  KCommandContext $context
	 * @return boolean         True if row can be updated, false otherwise
	 */
	protected function _beforeUpdate(KCommandContext $context)
	{
		return (bool) !$this->locked();
	}

	/**
	 * Checks if a row can be deleted
	 *
	 * This function determines if a row can be deleted based on it's locked_by information. If a row is locked, and
     * not by the logged in user, the function will return false, otherwise it will return true
	 *
     * @param  KCommandContext $context
     * @return boolean         True if row can be deleted, false otherwise
     */
	protected function _beforeDelete(KCommandContext $context)
	{
		return (bool) !$this->locked();
	}
}
