<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
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
     * Find a template path
     *
     * @param array  $info      The path information
     * @return bool|mixed
     */
    public function find(array $info)
    {
        $loader = $this->getObject('manager')->getClassLoader();

        //Get the package
        $package = $info['package'];

        //Get the domain
        $domain = $info['domain'];

        //Base path
        if(!empty($domain)) {
            $basepath = $loader->getNamespace($domain);
        } else {
            $basepath = $loader->getLocator('component')->getNamespace(ucfirst($package));
        }

        $basepath .= '/components/com_'.strtolower($package);

        //File path
        $filepath  = 'view/'.implode('/', $info['path']).'/tmpl/'.$info['file'].'.'.$info['format'].'.php';

        // Find the template
        $result = $this->realPath($basepath.'/'.$filepath);

        // Check for a views/ folder as well
        if ($result === false)
        {
            $filepath = 'views/'.substr($filepath, 5);

            $result = $this->realPath($basepath.'/'.$filepath);
        }

        return $result;
    }
}