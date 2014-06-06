<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Module Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Module\Koowa
 */
class ModKoowaTemplateLocatorModule extends KTemplateLocatorAbstract
{
    /**
     * Find a template path
     *
     * @param array  $info      The path information
     * @return bool|mixed
     */
    public function find(array $info)
    {
        $basepath  = $this->getObject('manager')->getClassLoader()->getNamespace($info['domain']);
        $basepath  = $basepath.'/modules/mod_'.strtolower($info['package']);

        $filepath   = (count($info['path']) ? implode('/', $info['path']).'/' : '').'tmpl';
        $filepath  .= $info['file'].'.'.$info['format'].'.php';

        // Find the template
        return $this->realPath($basepath.'/'.$filepath);
    }
}