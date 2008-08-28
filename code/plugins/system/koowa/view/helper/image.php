<?php
/**
 * @version		$Id$
 * @package		Koowa_View
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Image View Helper Class
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package		Koowa_View
 * @subpackage	Helper
 */
class KViewHelperImage
{
	/**
	 * Creates a tooltip with an image as button
	 *
	 * @param	string	$tooltip The tip string
	 * @param	string	$title The title of the tooltip
	 * @param	string	$image The image for the tip, if no text is provided
	 * @param	string	$text The text for the tip
	 * @param	string	$href An URL that will be used to create the link
	 * @param	boolean depreciated
	 * @return	string
	 * @since	1.5
	 */
	public static function tooltip($tooltip, $title='', $image='tooltip.png', $text='', $href='', $link=1)
	{
		$tooltip	= addslashes(htmlspecialchars($tooltip));
		$title		= addslashes(htmlspecialchars($title));

		if ( !$text ) {
			$image 	= JURI::root(true).'/includes/js/ThemeOffice/'. $image;
			$text 	= '<img src="'. $image .'" border="0" alt="'. JText::_( 'Tooltip' ) .'"/>';
		} else {
			$text 	= JText::_( $text, true );
		}

		if($title) {
			$title = $title.'::';
		}

		$style = 'style="text-decoration: none; color: #333;"';

		if ( $href ) {
			$href = JRoute::_( $href );
			$style = '';
			$tip = '<span class="editlinktip hasTip" title="'.$title.$tooltip.'" '. $style .'><a href="'. $href .'">'. $text .'</a></span>';
		} else {
			$tip = '<span class="editlinktip hasTip" title="'.$title.$tooltip.'" '. $style .'>'. $text .'</span>';
		}

		return $tip;
	}
	
}