<?php
/**
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Accordion Behavior Helper
 *
 * @author		Stian Didriksen <stian@timble.net>
 * @package		Koowa_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class ComKoowaTemplateHelperAccordion extends KTemplateHelperAbstract
{
	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param 	array|KConfig $config An optional array with configuration options
	 * @return 	string	Html
	 */
	public function startPane($config = array())
	{
		$config = new KConfig($config);

		$config->append(array(
			'id'	=> 'sliders',
			'options'	=> array(
				'duration'		=> 300,
				'opacity'		=> false,
				'alwaysHide'	=> true,
				'scroll'		=> false
			)
		));

        return JHtml::_('sliders.start', $config->id, KConfig::unbox($config->options));
	}

	/**
	 * Ends the pane
	 *
     * @param 	array|KConfig $config An optional array with configuration options
     *
	 * @return 	string	Html
	 */
	public function endPane($config = array())
	{
        return JHtml::_('sliders.end');
	}

	/**
	 * Creates a tab panel with title and starts that panel
	 *
     * @param 	array|KConfig $config An optional array with configuration options
     *
     * @return 	string	Html
	 */
	public function startPanel($config = array())
	{
		$config = new KConfig($config);

		$config->append(array(
			'title'		=> 'Slide',
			'id'     	=> '',
			'translate'	=> true
		));

		$title = $config->translate ? $this->translate($config->title) : $config->title;

        return JHtml::_('sliders.panel', $title, KConfig::unbox($config->attribs));
	}

	/**
	 * Ends a tab page
	 *
     * @param 	array|KConfig $config An optional array with configuration options
     *
	 * @return 	string	Html
	 */
	public function endPanel($config = array())
	{
		return '';
	}
}
