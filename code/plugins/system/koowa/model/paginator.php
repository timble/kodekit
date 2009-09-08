<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Pagination Model
 * 
 * To use, set the following states
 * 
 * total:  		Total number of items
 * limit:  		Number of items per page
 * offset: 		The starting item for the current page
 * display: 	Number of links to generate before and after the current offset,
 * 				or 0 for all (Optional)
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package     Koowa_Model
 */
class KModelPaginator extends KModelAbstract
{
	/**
	 * Paginate based on total, limit and offset
	 * 
	 * @return  KModelPaginator
	 */
    public function paginate()
    {
    	$total	= (int) $this->_state->total;
		$limit	= (int) max($this->_state->limit, 1);
		$offset	= (int) max($this->_state->offset, 0);

		if($limit > $total) {
			$offset = 0;
		}
		
		if(!$limit) 
		{
			$offset = 0;
			$limit  =  $total;
		}

		$pages_count	= (int) ceil($total / $limit);

    	if($offset > $total) {
			$offset = ($pages_count-1) * $limit;
		}

		$pages_current = (int) floor($offset / $limit) +1;

		$this->_state->set(array(
				'total'   => $total,
				'limit'   => $limit,
				'offset'  => $offset,
				'count'   => $pages_count,
				'current' => $pages_current
		));
		
		return $this;
    }
    
    /**
	 * Get a list of pages
	 *
	 * @return  array 	Returns and array of pages information
	 */
    public function getList()
    {
    	$elements = array();
    	$prototype = new KObject();
    	$current = ($this->_state->current - 1) * $this->_state->limit;

    	// First
    	$page = 1;
    	$offset = 0;
    	$active = $offset != $this->_state->offset;
    	$props = array('page' => $page, 'offset' => $offset, 'current' => false, 'active' => $active, 'text' => 'First');
    	$element 	= clone $prototype;
    	$elements[] = $element->set($props);

    	// Previous
    	$page = $this->_state->current - 1;
    	$offset = max(0, ($page - 1) * $this->_state->limit);
		$active = $offset != $this->_state->offset;
    	$props = array('page' => $page, 'offset' => $offset, 'current' => false, 'active' => $active, 'text' => 'Previous');
    	$element 	= clone $prototype;
    	$elements[] = $element->set($props);

		// Pages
		foreach($this->_getOffsets() as $page => $offset)
		{
			$current = $offset == $this->_state->offset;
			$props = array('page' => $page, 'offset' => $offset, 'current' => $current, 'active' => !$current, 'text' => $page);
    		$element 	= clone $prototype;
    		$elements[] = $element->set($props);
		}

		// Next
    	$page = $this->_state->current + 1;
    	$offset = min(
    				($this->_state->count-1) * $this->_state->limit,
    				($page - 1) * $this->_state->limit);
 		$active = $offset != $this->_state->offset;
    	$props = array('page' => $page, 'offset' => $offset, 'current' => false, 'active' => $active, 'text' => 'Next');
    	$element 	= clone $prototype;
    	$elements[] = $element->set($props);

    	// Last
    	$page = $this->_state->count;
    	$offset = ($page - 1) * $this->_state-limit;
    	$active = $offset != $this->_state->offset;
    	$props = array('page' => $page, 'offset' => $offset, 'current' => false, 'active' => $active, 'text' => 'Last');
    	$element 	= clone $prototype;
    	$elements[] = $element->set($props);

    	return $elements;
    }
    
 	/**
     * Get the offset for each page, optionally with a range
     *
     * @return 	array	Page number => offset
     */
	protected function _getOffsets()
    {
   	 	if($display = $this->_state->display)
    	{
    		$start	= (int) max($this->_state->current - $display, 1);
    		$start	= min($this->_state->count, $start);
    		$stop	= (int) min($this->_state->current + $display, $this->_state->count);
    	}
    	else // show all pages
    	{
    		$start = 1;
    		$stop = $this->_state->count;
    	}

    	$result = array();
    	foreach(range($start, $stop) as $pagenumber) {
    		$result[$pagenumber] = 	($pagenumber-1) * $this->_state->limit;
    	}

    	return $result;
    }
}