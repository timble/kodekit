<?php
/**
 * @version		$Id:proxy.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Pattern
 * @subpackage	Observer
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract observer class to implement the observer design pattern
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @package     Koowa_Pattern
 * @subpackage  Observer
 * @uses		KObject
 */
abstract class KPatternObserver extends KObject 
{
	/**
	 * Event object to observe
	 *
	 * @var object
	 */
	protected $_subject;

	/**
	 * Constructor
	 * 
	 * @param	object	$subject	The subject to observer
	 * @return	void
	 */
	public function __construct(KPatternObservable $subject)
	{
		// Register the observer ($this) so we can be notified
		$subject->attach($this);

		// Set the subject to observe
		$this->_subject = $subject;
	}

	/**
	 * Event received in case the observables states has changed
	 *
	 * @param	array	$args	An associative array of arguments
	 * @return mixed
	 */
	abstract public function onNotify(array $args);
}