<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @category		Koowa
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Default array filter
 * 
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Filter
 */
class KFilterArrayDefault extends KFilterArrayAbstract 
{
	/**
	 * Set a scalar filter to use for the FilterArray
	 *
	 * @param 	KFilterInterface $filter
	 * @return	this
	 */
	public function setFilter(KFilterInterface $filter)
	{
		$this->_filter = $filter;
		$this->setClassName(array(
				'prefix' => KInflector::getPart($filter, 0), 
				'base'=>'FilterArray', 
				'suffix'=>KInflector::getPart($filter, 1)
		));	
		return $this;
	}
}