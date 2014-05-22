<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class ComKoowaTemplateLocatorComponent extends KTemplateLocatorComponent
{
    /**
     * Handles template overrides
     *
     * @param  string $path
     * @return string File path
     */
    public function locate($path)
    {
        $result = parent::locate($path);

        $template  = JFactory::getApplication()->getTemplate();
        $override  = JPATH_THEMES.'/'.$template.'/html';
        $override .= str_replace(array(JPATH_BASE.'/modules', JPATH_BASE.'/components', '/views', '/tmpl'), '', $path);

        $override = $this->realPath($override);

        if ($override) {
            $result = $override;
        }

        return $result;
    }
}