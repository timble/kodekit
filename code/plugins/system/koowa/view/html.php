<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_View
 * @subpackage  Html
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * View HTML Class
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_View
 * @subpackage  Html
 */

class KViewHtml extends KViewAbstract
{
	public function __construct($options = array())
	{
		// add a rule to the template for KSecurityToken
		KTemplateDefault::addRules(array(KFactory::get('lib.koowa.template.rule.tokens')));
		
		parent::__construct($options);
	}
	
	public function display($tpl = null)
	{
		$prefix = $this->getClassName('prefix');

		//Set the main stylesheet for the component
		KViewHelper::_('stylesheet', $prefix.'.css', 'media/com_'.$prefix.'/css/');

		parent::display($tpl);
	}
}
