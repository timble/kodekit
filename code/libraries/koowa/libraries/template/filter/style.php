<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Style Template Filter
 *
 * Filter to parse style tags
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterStyle extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

	/**
	 * Find any <style src"" /> or <style></style> elements and render them
	 *
     * @param string $text Block of text to parse
     * @return $this
     */
	public function write(&$text)
	{
		//Parse the script information
		$styles = $this->_parseStyles($text);

		//Prepend the script information
		$text = $styles.$text;

		return $this;
	}

	/**
	 * Parse the text for style tags
	 *
	 * @param 	string 	$text Block of text to parse
	 * @return 	string
	 */
	protected function _parseStyles(&$text)
	{
		$styles = '';

		$matches = array();
		if(preg_match_all('#<style\s*src="([^"]+)"(.*)\/>#iU', $text, $matches))
		{
			foreach(array_unique($matches[1]) as $key => $match)
			{
				$attribs = $this->parseAttributes( $matches[2][$key]);
				$styles .= $this->_renderStyle($match, true, $attribs);
			}

			$text = str_replace($matches[0], '', $text);
		}

		$matches = array();
		if(preg_match_all('#<style(.*)>(.*)<\/style>#siU', $text, $matches))
		{
			foreach($matches[2] as $key => $match)
			{
				$attribs = $this->parseAttributes( $matches[1][$key]);
				$styles .= $this->_renderStyle($match, false, $attribs);
			}

			$text = str_replace($matches[0], '', $text);
		}

		return $styles;
	}

	/**
	 * Render style information
	 *
     * @param string	$style   The script information
     * @param boolean	$link    True, if the script information is a URL.
     * @param array		$attribs Associative array of attributes
     * @return string
     */
	protected function _renderStyle($style, $link, $attribs = array())
	{
		$attribs = $this->buildAttributes($attribs);

		if(!$link)
		{
			$html  = '<style type="text/css" '.$attribs.'>'."\n";
			$html .= trim($style['data']);
			$html .= '</style>'."\n";
		}
		else $html = '<link type="text/css" rel="stylesheet" href="'.$style.'" '.$attribs.' />'."\n";

		return $html;
	}
}
