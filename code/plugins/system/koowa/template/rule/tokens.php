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
 * Template rule to add security tokens
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage	Rule 
 */
class KTemplateRuleTokens extends KObject implements KTemplateRuleInterface
{
	/**
	 * Add unique token field 
	 *
	 * @param string $text
	 */
	public function parse(&$text) 
	{		 
		// TODO don't add the field when method='get'
		// TODO add support for <?=@disable_token 
		 
        if(strpos($text, '</form>') && !strpos($text, 'KSecurityToken')) 
        {
        	$text = str_replace(
        		'</form>', 
        		'<?php echo KSecurityToken::render()?>'.PHP_EOL.'</form>', 
        		$text
        	);
        }
	}
}