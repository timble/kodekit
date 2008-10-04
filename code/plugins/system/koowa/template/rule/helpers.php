<?php
/**
* @version      $Id$
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Rule
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.koowa.org
*/

/**
 * Template rule for helper tags such as @template, @text, @helper, @route
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage	Rule 
 */
class KTemplateRuleHelpers extends KObject implements KTemplateRuleInterface
{
	/**
	 * Tags => replacement
	 *
	 * @var array
	 */
	protected $_tags = array(
		'@template('	=> '$this->loadTemplate(',
		'@text('	 	=> 'JText::_(',
		'@helper('   	=> '$this->loadHelper(',
		'@route('    	=> '$this->createRoute('
	);
	
	/**
	 * Convert the tags
	 *
	 * @param string $text
	 */
	public function parse(&$text) 
	{
		$text = str_replace(
			array_keys($this->_tags), 
			array_values($this->_tags), 
			$text);
	}
}


			