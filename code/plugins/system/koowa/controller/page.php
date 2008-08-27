<?php
/**
 * @version		$Id$
 * @package     Koowa_Controller
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Page Controller Class
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @author 		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Controller
 * @uses        KSecurityToken
 * @uses        KInflector
 * @uses        KHelperArray
 */
class KControllerPage extends KControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Register extra tasks
		$this->registerTask( 'disable', 'enable');
		$this->registerTask( 'apply'  , 'save'  );
		$this->registerTask( 'add'    , 'edit'  );
	}

	/*
	 * Generic edit action
	 */
	public function edit()
	{
		$cid = KRequest::get('cid', 'get', KFactory::get('lib.koowa.filter.array.ints'), null, array(0));
		$id	 = KRequest::get('id', 'get', KFactory::get('lib.koowa.filter.int'), null, $cid[0]);
		 
		$this->setRedirect('view='.$this->getClassName('suffix').'&layout=form&id='.$id);
	}

	/*
	 * Generic save action
	 */
	public function save()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		// Get the post data from the request
		$data = $this->_getRequest('post');

		// Get the id
		$id	 = KRequest::get('id', 'request', KFactory::get('lib.koowa.filter.int'));

		// Get the table object attached to the model
		$suffix = $this->getClassName('suffix');
		$prefix = $this->getClassName('prefix');

		$table = KFactory::get('com.'.$prefix.'.model.'.$suffix)->getTable();
		
		if (!empty($id)) {
			$ret = $table->update($data, $id);
		} else {
			$ret = $table->insert($data);
			$id  = $table->getDBO()->insertid();
		}

		$redirect = 'format='.KRequest::get('format', 'get', KFactory::get('lib.koowa.filter.cmd'), null, 'html');
		switch($this->getTask())
		{
			case 'apply' :
				$redirect = '&view='.$suffix.'&layout=form&id='.$id;
				break;

			case 'save' :
			default     :
				$redirect = '&view='.KInflector::pluralize($suffix);
		}

		$this->setRedirect($redirect);
	}
		
	/*
	 * Generic cancel action
	 */
	public function cancel()
	{
		$this->setRedirect(
			'view='.KInflector::pluralize($this->getClassName('suffix'))
			.'&format='.KRequest::get('format', 'get', KFactory::get('lib.koowa.filter.cmd'), null, 'html')
			);
	}
	
	/*
	 * Generic delete function
	 *  
	 * @throws KControllerException
	 */
	public function delete()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		$cid = KRequest::get('cid', 'post', KFactory::get('lib.koowa.filter.array.ints'), null, array());

		if (count( $cid ) < 1) {
			throw new KControllerException(JText::sprintf( 'Select an item to %s', JText::_($this->getTask()), true ) );
		}

		// Get the table object attached to the model
		$suffix = $this->getClassName('suffix');
		$prefix = $this->getClassName('prefix');

		$table = KFactory::get('com.'.$prefix.'.model.'.$suffix)->getTable();
		$table->delete($cid);
		
		$this->setRedirect(
			'view='.KInflector::pluralize($suffix)
			.'&format='.KRequest::get('format', 'get', KFactory::get('lib.koowa.filter.cmd'), null, 'html')
		);
	}

	/*
	 * Generic enable action
	 */
	public function enable()
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
	
		$cid = KRequest::get('cid', 'post', KFactory::get('lib.koowa.filter.array.ints'), null, array());

		$enable  = $this->getTask() == 'enable' ? 1 : 0;

		if (count( $cid ) < 1) {
			throw new KControllerException(JText::sprintf( 'Select a item to %s', JText::_($this->getTask()), true ));
		}

		// Get the table object attached to the model
		$suffix = $this->getClassName('suffix');
		$prefix = $this->getClassName('prefix');

		$table = KFactory::get('com.'.$prefix.'.model.'.$suffix)->getTable();
		$table->update(array('enabled' => $enable), $cid);
	
		$this->setRedirect(
			'view='.KInflector::pluralize($suffix)
			.'&format='.KRequest::get('format', 'get', KFactory::get('lib.koowa.filter.cmd'), null, 'html')
		);
	}
	
	/**
	 * Generic method to modify the access level of items
	 */
	public function access($access)
	{
		KSecurityToken::check() or die('Invalid token or time-out, please try again');
		
		$cid 	= KRequest::get('cid', 'post', KFactory::get('lib.koowa.filter.array.ints'), null, array());
		$access = KRequest::get('access', 'post', KFactory::get('lib.koowa.filter.int'));
		
		// Get the table object attached to the model
		$suffix = $this->getClassName('suffix');
		$prefix = $this->getClassName('prefix');

		$table = KFactory::get('com.'.$prefix.'.model.'.$suffix)->getTable();
		$table->update(array('access' => $access), $cid);
	
		$this->setRedirect(
			'view='.KInflector::pluralize($suffix)
			.'&format='.KRequest::get('format', 'get', KFactory::get('lib.koowa.filter.cmd'), null, 'html'), 
			JText::_( 'Changed items access level')
		);
	}

	/**
	 * Wrapper for JRequest::get(). Override this method to modify the GET/POST data before saving
	 *
	 * @see		JRequest::get()
	 * 
	 * @param	string	$hash	to get (POST, GET, FILES, METHOD)
	 * @param	int		$mask	Filter mask for the variable
	 * @return	mixed	Request hash
	 * @return array
	 */
	protected function _getRequest($hash = 'default', $mask = 0)
	{
		return JRequest::get($hash, $mask);
	}
}
