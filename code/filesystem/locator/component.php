<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Component Translator Locator
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Translator\Locator
 */
class FilesystemLocatorComponent extends FilesystemLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'com';

    /**
     * Get the list of path templates
     *
     * @param  string $url The language url
     * @return array The path templates
     */
    public function getPathTemplates($url)
    {
        $templates = parent::getPathTemplates($url);

        foreach($templates as $key => $template)
        {
            //Make relative paths absolute
            if(substr($template, 0, 1) !== '/')
            {
                unset($templates[$key]);

                $info  = $this->parseUrl($url);
                $paths = $this->getObject('object.bootstrapper')->getComponentPaths($info['package'], $info['domain']);

                $inserts = array();
                foreach ($paths as $path) {
                    $inserts[] = $path . '/'. $template;
                }

                //Insert the paths at the right position in the array
                array_splice( $templates, $key, 0, $inserts);
            }
        }

        return $templates;
    }
}
