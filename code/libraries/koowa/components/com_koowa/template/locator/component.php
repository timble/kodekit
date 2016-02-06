<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Component Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Locator
 */
class ComKoowaTemplateLocatorComponent extends KTemplateLocatorComponent
{
    /**
     * The override path
     *
     * @var string
     */
    protected static $_override_paths;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!isset($config->override_paths) && !self::$_override_paths) {
            self::$_override_paths = array_values($this->getObject('com::koowa.model.templates')->active(1)->fetch()->toArray());
        }
    }

    /**
     * Find a template path
     *
     * @param array  $info      The path information
     * @return bool|mixed
     */
    public function find(array $info)
    {
        $paths  = array();

        //Get the package
        $package = $info['package'];

        //Get the domain
        $domain = $info['domain'];

        /*
         * Theme path
         */
        if(!empty(self::$_override_paths))
        {
            //Remove the 'view' element from the path.
            $path = $info['path'];
            if(isset($path[0]) && $path[0] == 'view') {
                array_shift($path);
            }

            //Find the template file
            if(!empty($info['type'])) {
                $filepath = 'com_'.$package.'/'.implode('/', $path).'/'.$info['file'].'.'.$info['format'].'.'.$info['type'];
            } else {
                $filepath = 'com_'.$package.'/'.implode('/', $path).'/'.$info['file'].'.'.$info['format'].'.*';
            }

            $index = $domain === 'site' ? 0 : 1;

            $paths[] = self::$_override_paths[$index]['path'].'/html/'.$filepath;
        }

        /*
         * Component path
         */

        //Check if we are trying to find a template inside an application component
        if($path = $this->getObject('object.bootstrapper')->getApplicationPath($domain)) {
            $basepath = $path.'/com_'.strtolower($package);
        } else {
            $basepath = $this->getObject('object.bootstrapper')->getComponentPath($package);
        }

        //If no type exists create a glob pattern
        if(!empty($info['type'])) {
            $filepath = implode('/', $info['path']).'/tmpl/'.$info['file'].'.'.$info['format'].'.'.$info['type'];
        } else {
            $filepath = implode('/', $info['path']).'/tmpl/'.$info['file'].'.'.$info['format'].'.*';
        }

        $paths[] = $basepath.'/view*/'.$filepath;

        foreach($paths as $path)
        {
            $results = glob($path);

            //Find the file in the directory
            if ($results)
            {
                foreach($results as $file)
                {
                    if($result = $this->realPath($file)) {
                        return $result;
                    }
                }
            }

        }

        return false;
    }
}