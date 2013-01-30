<?php
/**
 * @version		$Id$
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Helper Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperTranslator extends KTemplateHelperAbstract
{
    protected static $_translator;
    
    public function translate($string, $parameters = array())
    {
	    return $this->getTranslator()->translate($string, $parameters);
    }
    
    public function getTranslator()
    {
	    if (!self::$_translator instanceof KTranslator) {
	        $identifier = clone $this->getTemplate()->getIdentifier();
	        $identifier->path = array();
	        $identifier->name = 'translator';
	        
	        self::$_translator = $this->getService($identifier);
	    }
        
	    return self::$_translator;
    }
}