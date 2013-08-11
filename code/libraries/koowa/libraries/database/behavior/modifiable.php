<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Database Modifiable Behavior
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage 	Behavior
 */
class KDatabaseBehaviorModifiable extends KDatabaseBehaviorAbstract
{
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return void
     */
	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'priority'   => KCommand::PRIORITY_LOW,
	  	));

    	parent::_initialize($config);
   	}

    /**
     * Get the methods that are available for mixin based
     *
     * This function conditionally mixes the behavior. Only if the mixer
     * has a 'created_by' or 'created_on' property the behavior will be
     * mixed in.
     *
     * @param KObject $mixer The mixer requesting the mixable methods.
     * @return array         An array of methods
     */
	public function getMixableMethods(KObject $mixer = null)
	{
		$methods = array();

		if(isset($mixer->modified_by) || isset($mixer->modified_on)) {
			$methods = parent::getMixableMethods($mixer);
		}

		return $methods;
	}

	/**
	 * Set modified information
	 *
	 * Requires a 'modified_on' and 'modified_by' column
	 *
     * @param KCommandContext $context
	 * @return void
	 */
	protected function _beforeTableUpdate(KCommandContext $context)
	{
		//Get the modified columns
		$modified = $this->getTable()->filter(array_flip($this->getModified()));

		if(!empty($modified))
		{
			if(isset($this->modified_by)) {
				$this->modified_by = (int) JFactory::getUser()->get('id');
			}

			if(isset($this->modified_on)) {
				$this->modified_on = gmdate('Y-m-d H:i:s');
			}
		}
	}
}
