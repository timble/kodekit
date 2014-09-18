<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Sluggable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Database\Behavior
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
        $filter = $this->getObject('com:koowa.filter.alias', $config);

        return $filter;
    }
    
    /**
     * Make sure the slug is unique
     *
     * This function checks if the slug already exists and if so appends a number to the slug to make it unique. The
     * slug will get the form of slug-x.
     *
     * If the slug is empty it returns the current date in the format Y-m-d-H-i-s
     *
     * @return void
     */
    protected function _canonicalizeSlug()
    {
        if (trim(str_replace($this->_separator, '', $this->slug)) == '') {
            $this->slug = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        parent::_canonicalizeSlug();
    }
}
