<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Template read filter to convert @ to $this->
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage	Filter
 */
class KTemplateFilterVariable extends KTemplateFilterAbstract implements KTemplateFilterRead
{
	/**
	 * Convert '@' to '$this->', unless when they are escaped '\@'
	 *
	 * @param string
	 * @return KTemplateFilterVariable
	 */
	public function read(&$text)
	{
        /**
         * We could make a better effort at only finding @ between <?php ?>
         * but that's probably not necessary as @ doesn't occur much in the wild
         * and there's a significant performance gain by using str_replace().
         */

		// Replace \@ with \$
		$text = str_replace('\@', '\$', $text);

        // Now replace non-eescaped @'s
         $text = str_replace(array('@$'), '$', $text);

        // Replace \$ with @
		$text = str_replace('\$', '@', $text);

		return $this;
	}
}
