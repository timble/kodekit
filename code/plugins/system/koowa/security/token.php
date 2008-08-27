<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Security
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Utility class to work with tokens in forms, to prevent CSRF attacks
 *
 * @example:
 * In a form:
 * <code>
 * <?php echo KSecurityToken::render();?>
 * </code>
 * Where the form is submitted:
 * <code>
 * <?php KSecurityToken::check() or die('Invalid Token'); ?>
 * </code>
 * 
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Security
 */
class KSecurityToken
{
	/**
	 * Token
	 *
	 * @var	string
	 */
	protected static $_token;
	
    /**
     * Generate new token and store it in the session
     * 
     * @param	bool	Force to generate a new token
     * @return	string	Token
     */
    static public function get($forceNew = false)
    {
        if($forceNew || !isset(self::$_token))
        {
            self::$_token 	= md5(uniqid(rand(), TRUE));
            $session 		= JFactory::getSession()->set('koowa.security.token', self::$_token);
            $session 		= JFactory::getSession()->set('koowa.security.tokentime', time());
        }

        return self::$_token;
    }

    /**
     * Render the hidden input field with the token
     *
     * @return	string	Html hidden input field
     */
    static public function render()
    {
    	return '<input type="hidden" name="_token" value="'.self::get().'" />';
    }

    /**
     * Check if a valid token was submitted
     *
     * @param 	boolean	Maximum age, defaults to 600 seconds
     * @return	boolean	True on success
     */
    static public function check($max_age = 600)
    {
    	$session	= JFactory::getSession();
        $token		= $session->get('koowa.security.token', null);
		$age 		= time() - $session->get('koowa.security.tokentime');
		
        $req		= KRequest::get('_token', 'post', KFactory::get('lib.koowa.filter.md5')); 
		
        return ($req===$token && $age <= $max_age);
    }
}