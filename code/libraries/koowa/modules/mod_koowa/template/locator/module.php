<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Module Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Module\Koowa\Template\Locator\Module
 */
class ModKoowaTemplateLocatorModule extends KTemplateLocatorIdentifier
{
    /**
     * The stream name
     *
     * @var string
     */
    protected static $_name = 'mod';

    /**
     * Get the locator name
     *
     * @return string The stream name
     */
    public static function getName()
    {
        return static::$_name;
    }

    /**
     * Find a template path
     *
     * @param array  $info      The path information
     * @return bool|mixed
     */
    public function find(array $info)
    {
        $locator = $this->getObject('manager')->getClassLoader()->getLocator('module');

        //Get the package
        $package = $info['package'];

        //Get the domain
        $domain = $info['domain'];

        //Switch basepath
        if(!$locator->getNamespace(ucfirst($package))) {
            $basepath = $locator->getNamespace('\\');
        } else {
            $basepath = $locator->getNamespace(ucfirst($package));
        }

        $basepath .= '/mod_'.strtolower($package);

        $filepath   = (count($info['path']) ? implode('/', $info['path']).'/' : '').'tmpl/';
        $filepath  .= $info['file'].'.'.$info['format'].'.php';

        // Find the template
        return $this->realPath($basepath.'/'.$filepath);
    }
}