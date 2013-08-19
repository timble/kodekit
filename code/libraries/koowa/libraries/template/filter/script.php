<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Script Template Filter
 *
 * Filter to parse script tags
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterScript extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KTemplateFilter::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

	/**
	 * Find any <script src="" /> or <script></script> elements and render them
	 *
	 * <script inline></script> can be used for inline scripts
	 *
	 * @param string $text Block of text to parse
	 * @return $this
	 */
	public function write(&$text)
	{
		//Parse the script information
		$scripts = $this->_parseScripts($text);

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
	protected function _parseScripts(&$text)
	{
		$scripts = '';

		$matches = array();
		// <script src="" />
		if(preg_match_all('#<script(?!\s+data-inline\s*)\s+src="([^"]+)"(.*)/>#siU', $text, $matches))
		{
			foreach(array_unique($matches[1]) as $key => $match)
			{
				$attribs = $this->parseAttributes( $matches[2][$key]);

                if (!isset($attribs['type'])) {
                    $attribs['type'] = 'text/javascript';
                }

                if($attribs['type'] == 'text/javascript')
                {
                    $scripts .= $this->_renderScript($match, true, $attribs);
                }
			}

			$text = str_replace($matches[0], '', $text);
		}

		$matches = array();
		// <script></script>
		if(preg_match_all('#<script(?!\s+data-inline\s*)(.*)>(.*)</script>#siU', $text, $matches))
		{
			foreach($matches[2] as $key => $match)
			{
				$attribs = $this->parseAttributes( $matches[1][$key]);
				$scripts .= $this->_renderScript($match, false, $attribs);
			}

			$text = str_replace($matches[0], '', $text);
		}

		return $scripts;
	}

	/**
	 * Render script information
	 *
	 * @param string	$script  The script information
	 * @param boolean	$link    True, if the script information is a URL.
	 * @param array		$attribs Associative array of attributes
	 * @return string
	 */
	protected function _renderScript($script, $link, $attribs = array())
	{
		$attribs = $this->buildAttributes($attribs);

		if(!$link)
		{
			$html  = '<script type="text/javascript" '.$attribs.'>'."\n";
			$html .= trim($script);
			$html .= '</script>'."\n";
		}
		else $html = '<script type="text/javascript" src="'.$script.'" '.$attribs.'></script>'."\n";

		return $html;
	}
}
