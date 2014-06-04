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
class KTemplateLocatorComponent extends KTemplateLocatorAbstract
{
    /**
     * The type
     *
     * @var string
     */
    protected $_type = 'com';

    /**
     * Locate the template based on a virtual path
     *
     * @param  string $path  Stream path or resource
     * @param  string $base  The base path or resource (used to resolved partials).
     * @throws RuntimeException If the no base path was passed while trying to locate a partial.
     * @return string   The physical stream path for the template
     */
    public function locate($path, $base = null)
    {
        //Qualify partial templates.
        if(strpos($path, ':') === false)
        {
            if(empty($base)) {
                throw new RuntimeException('Cannot qualify partial template path');
            }

            $identifier = $this->getIdentifier($base)->toArray();

            $format    = pathinfo($path, PATHINFO_EXTENSION);
            $template  = pathinfo($path, PATHINFO_FILENAME);

            $parts     = $identifier['path'];
            array_pop($parts);
        }
        else
        {
            // Need to clone here since we use array_pop and it modifies the cached identifier
            $identifier = $this->getIdentifier($path)->toArray();

            $format    = $identifier['name'];
            $template  = array_pop($identifier['path']);
            $parts     = $identifier['path'];
        }

        $package   = $identifier['package'];
        $domain    = $identifier['domain'];

        //Find the base path
        if(!empty($domain)) {
            $rootpath = $this->getObject('manager')->getClassLoader()->getBasepath($domain);
        } else {
            $rootpath  = $this->getObject('manager')->getClassLoader()->getLocator('component')->getNamespace(ucfirst($package));
        }

        $basepath  = $rootpath.'/components/com_'.strtolower($package);
        $filepath  = 'views/'.implode('/', $parts).'/tmpl/'.$template.'.'.$format.'.php';

        // Find the template
        $result = $this->realPath($basepath.'/'.$filepath);

        return $result;
    }
}