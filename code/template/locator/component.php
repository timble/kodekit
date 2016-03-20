<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Component Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Locator
 */
class TemplateLocatorComponent extends TemplateLocatorIdentifier
{
    /**
     * The stream name
     *
     * @var string
     */
    protected static $_name = 'com';

    /**
     * Find a template path
     *
     * @param array  $info      The path information
     * @return string|false The real template path or FALSE if the template could not be found
     */
    public function find(array $info)
    {
        $result = false;

        //Base paths
        $paths = $this->getObject('object.bootstrapper')->getComponentPath($info['package'], $info['domain']);

        //If no type exists create a glob pattern
        if(!empty($info['type'])){
            $filepath =  implode('/', $info['path']).'/templates/'.$info['file'].'.'.$info['format'].'.'.$info['type'];
        } else {
            $filepath =  implode('/', $info['path']).'/templates/'.$info['file'].'.'.$info['format'].'.*';
        }

        foreach($paths as $basepath)
        {
            $pattern = $basepath .'/view/'. $filepath;
            $results = glob($pattern);

            //Try to find the file
            if ($results)
            {
                foreach($results as $file)
                {
                    if($result = $this->realPath($file)) {
                        break (2);
                    }
                }
            }
        }

        return $result;
    }
}