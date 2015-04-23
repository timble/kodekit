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
 * @package Koowa\Library\Translator\Locator
 */
class KTranslatorLocatorComponent extends KTranslatorLocatorIdentifier
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'com';

    /**
     * Find a template path
     *
     * @param array  $info      The path information
     * @return array
     */
    public function find(array $info)
    {
        $paths  = array();
        $loader = $this->getObject('manager')->getClassLoader();

        //Base paths
        $namespace = $this->getObject('object.bootstrapper')->getComponentNamespace($info['package']);
        if($path = $loader->getLocator('component')->getNamespace($namespace)) {
            $paths[] = $path;
        }

        if($path = $loader->getLocator('component')->getNamespace('\\')) {
            $paths[] = $path.'/'.$info['package'];
        }

        $result = array();
        foreach($paths as $basepath)
        {
            $info['path'] = $basepath.'/resources/language';

            if($path = parent::find($info)) {
                $result = array_merge($result, $path);
            }
        }

        return $result;
    }
}
