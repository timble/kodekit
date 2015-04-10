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
class ComKoowaTranslatorLocatorComponent extends KTranslatorLocatorIdentifier
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
        //Get the package
        $package = $info['package'];

        //Get the domain
        $domain = $info['domain'];

        //Check if we are trying to find a template inside an application component
        if($path = $this->getObject('object.bootstrapper')->getApplicationPath($domain)) {
            $path = $path.'/com_'.strtolower($package);
        } else {
            $path = $this->getObject('object.bootstrapper')->getComponentPath($package).'/resources';
        }

        return array('com_' . $package => $path);
    }
}
