<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Default View Controller
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaControllerResource extends KControllerResource
{
    /**
     * Display action
     *
     * If the controller was not dispatched manually load the langauges files
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
     */
    protected function _actionGet(KCommandContext $context)
    {
        $this->getService('translator')->loadLanguageFiles($this->getIdentifier());

        return parent::_actionGet($context);
    }

	/**
     * Set a request property
     *
     *  This function translates 'limitstart' to 'offset' for compatibility with Joomla
     *
     * @param  	string 	The property name.
     * @param 	mixed 	The property value.
     */
 	public function __set($property, $value)
    {
        if($property == 'limitstart') {
            $property = 'offset';
        }

        parent::__set($property, $value);
  	}
}
