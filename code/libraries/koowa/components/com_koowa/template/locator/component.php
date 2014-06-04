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
     * Locate the template based on a virtual path
     *
     * @param  string $path  Stream path or resource
     * @param  string $base  The base path or resource (used to resolved partials).
     * @throws \RuntimeException If the no base path was passed while trying to locate a partial.
     * @return string   The physical stream path for the template
     */
    public function locate($path, $base = null)
    {
        $result = parent::locate($path, $base);

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