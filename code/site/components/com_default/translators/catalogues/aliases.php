<?php
/**
 * @version		$Id$
 * @package		Koowa_Translator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Translator Class
 *
 * @author		Ercan Ozkaya <ercan@timble.net>
 * @package		Koowa_Translator
 */
class ComDefaultTranslatorCatalogueAliases extends KTranslatorCatalogue
{
    protected function _initialize(KConfig $config)
    {
        $defaults = array(
            'all' => 'JALL',
            'title' => 'JGLOBAL_TITLE',
            'alias' => 'JFIELD_ALIAS_LABEL',
            'status' => 'JSTATUS',
            'category' => 'JCATEGORY',
            'categories' => 'JCATEGORIES',
            'access' => 'JGRID_HEADING_ACCESS',
            'date' => 'JDATE',
            'ordering' => 'JGRID_HEADING_ORDERING',
            'search' => 'JSEARCH_FILTER_SUBMIT',
            'clear' => 'JSEARCH_FILTER_CLEAR',
            'details' => 'JDETAILS',
            'description' => 'JGLOBAL_DESCRIPTION',
            'apply' => 'JAPPLY',
            'cancel' => 'JCANCEL',
            'created date' => 'JGLOBAL_FIELD_CREATED_LABEL',
            'created by' => 'JGLOBAL_FIELD_CREATED_BY_LABEL',
            'modified date' => 'JGLOBAL_FIELD_MODIFIED_LABEL',
            'last modified' => 'JGLOBAL_FIELD_MODIFIED_LABEL',
            'modified by' => 'JGLOBAL_FIELD_MODIFIED_BY_LABEL',
            'published' => 'JPUBLISHED',
            'unpublished' => 'JUNPUBLISHED',
            'metadata options' => 'JGLOBAL_FIELDSET_METADATA_OPTIONS',
            'options' => 'JOPTIONS',
            'unpublish item' => 'JLIB_HTML_UNPUBLISH_ITEM',
            'publish item' => 'JLIB_HTML_PUBLISH_ITEM',
            'move down' => 'JLIB_HTML_MOVE_DOWN',
            'move up' => 'JLIB_HTML_MOVE_UP',
            'select' => 'JSELECT',
            'yes' => 'JYES',
            'no' => 'JNO',
        );
        
        if (version_compare(JVERSION, '1.6', '>=')) {
            $config->append(array(
            'data'  => $defaults
            ));
        }
    
        parent::_initialize($config);
    }
}
