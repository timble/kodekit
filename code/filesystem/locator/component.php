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
     * Register a path template
     *
     * If the template path has format com:foo]/path/to/file this method will replace com:foo with the
     * base path of the component. If the component has additional paths a template path for each will
     * be added in FIFO order.
     *
     * @param  string $template   The path template
     * @param  bool $prepend      If true, the template will be prepended instead of appended.
     * @return FilesystemLocatorAbstract
     */
    public function registerPathTemplate($template, $prepend = false)
    {
        if(parse_url($template, PHP_URL_SCHEME) === 'com')
        {
            $bootstrapper = $this->getObject('object.bootstrapper');

            $info    = $this->parseUrl($template);
            $package = $info['package'];
            $domain  = $info['domain'];

            //Remove component identifier from the template
            $identifier = $bootstrapper->getComponentIdentifier($package, $domain);
            $template   = ltrim(str_replace($identifier, '', $template), '/');

            $paths = $bootstrapper->getComponentPaths($package, $domain);
            foreach ($paths as $path)
            {
                $path = $path .'/' . $template;
                parent::registerPathTemplate($path, $prepend);
            }
        }
        else parent::registerPathTemplate($template, $prepend);

        return $this;
    }

    /**
     * Get the list of path templates
     *
     * This method will qualify relative url's (url not starting with '/') by prepending the component base
     * path to the url. If the component has additional paths a template path for each will be inserted in
     * FIFO order.
     *
     * @param  string $url The language url
     * @return array The path templates
     */
    public function getPathTemplates($url)
    {
        $templates = parent::getPathTemplates($url);

        foreach($templates as $key => $template)
        {
            //Qualify relative path
            if(substr($template, 0, 1) !== '/')
            {
                $bootstrapper = $this->getObject('object.bootstrapper');

                unset($templates[$key]);

                $info  = $this->parseUrl($url);
                $paths = $bootstrapper->getComponentPaths($info['package'], $info['domain']);

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
