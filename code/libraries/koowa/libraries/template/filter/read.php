<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Template Write Filter Interface
 *
 * Processes the template on input
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage  Filter
 */
interface KTemplateFilterRead
{
    /**
     * Parse the text and filter it
     *
     * @param string Block of text to parse
     * @return KTemplateFilterRead
     */
    public function read(&$text);
}
