<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Link Template Filter
 *
 * Filter to parse link tags
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterLink extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
	/**
	 * Find any <link /> elements and render them
	 *
	 * @param string $text Block of text to parse
	 * @return KTemplateFilterLink
	 */
	public function write(&$text)
	{
		//Parse the script information
		$scripts = $this->_parseLinks($text);

		//Prepend the script information
		$text = $scripts.$text;

		return $this;
	}

	/**
	 * Parse the text for script tags
	 *
	 * @param string $text Block of text to parse
	 * @return string
	 */
	protected function _parseLinks(&$text)
	{
		$scripts = '';

		$matches = array();
		if(preg_match_all('#<link\ href="([^"]+)"(.*)\/>#iU', $text, $matches))
		{
			foreach(array_unique($matches[1]) as $key => $match)
			{
				$attribs = $this->_parseAttributes( $matches[2][$key]);
				$scripts .= $this->_renderScript($match, $attribs);
			}

			$text = str_replace($matches[0], '', $text);
		}

		return $scripts;
	}

	/**
	 * Render script information
	 *
	 * @param string	$link       The script information
	 * @param array		$attribs    Associative array of attributes
	 * @return string
	 */
	protected function _renderLink($link, $attribs = array())
	{
		$attribs = KHelperArray::toString($attribs);

		$html = '<link href="'.$link.'" '.$attribs.'/>'."\n";
		return $html;
	}
}
