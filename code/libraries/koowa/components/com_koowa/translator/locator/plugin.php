<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Component Translator Locator
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Translator\Locator
 */
class ComKoowaTranslatorLocatorPlugin extends KTranslatorLocatorIdentifier
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'plg';

    /**
     * Find a template path
     *
     * @param array  $info      The path information
     * @return array
     */
    public function find(array $info)
    {
        $locator = $this->getObject('manager')->getClassLoader()->getLocator('plugin');

        //Get the package
        $package = $info['package'];

        //Get the name
        $name   = $info['url']->getName();

        //Switch basepath
        if(!$locator->getNamespace(ucfirst($package))) {
            $basepath = $locator->getNamespace('\\');
        } else {
            $basepath = $locator->getNamespace(ucfirst($package));
        }

        $basepath .= '/'.$package.'/'.$name;

        return array('plg_'.$package.'_'.$name => $basepath);
    }
}
