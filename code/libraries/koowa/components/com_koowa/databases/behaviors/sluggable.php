<?php
/**
 * @version     $Id: abstract.php 1528 2010-01-26 23:14:08Z johan $
 * @package     Koowa_Database
 * @subpackage  Behavior
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Database Sluggable Behavior
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage  Behavior
 */
class ComKoowaDatabaseBehaviorSluggable extends KDatabaseBehaviorSluggable
{
    /**
     * Create a sluggable filter
     *
     * Uses ComKoowaFilterAlias to create aliases based on Joomla settings
     *
     * @return KFilterInterface
     */
    protected function _createFilter()
    {
        $config = array();
        $config['separator'] = $this->_separator;

        if(!isset($this->_length)) {
            $config['length'] = $this->getTable()->getColumn('slug')->length;
        } else {
            $config['length'] = $this->_length;
        }

        //Create the filter
        $filter = $this->getService('com:koowa.filter.alias', $config);

        return $filter;
    }
    
    /**
     * Make sure the slug is unique
     *
     * This function checks if the slug already exists and if so appends
     * a number to the slug to make it unique. The slug will get the form
     * of slug-x.
     *
     * If the slug is empty it returns the current date in the format Y-m-d-H-i-s
     *
     * @return void
     */
    protected function _canonicalizeSlug()
    {
        parent::_canonicalizeSlug();

        if (trim(str_replace($this->_separator, '', $this->slug)) == '') {
        	$this->slug = JFactory::getDate()->format('Y-m-d-H-i-s');
        }
    }
}