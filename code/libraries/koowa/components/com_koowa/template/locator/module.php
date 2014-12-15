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
 * @package Koowa\Component\Koowa\Template\Locator
 */
class ComKoowaTemplateLocatorModule extends KTemplateLocatorIdentifier
{
    /**
     * The stream name
     *
     * @var string
     */
    protected static $_name = 'mod';

    /**
     * The override path
     *
     * @var string
     */
    protected $_override_path;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_override_path = $config->override_path;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional KObjectConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $template  = JFactory::getApplication()->getTemplate();

        $config->append(array(
            'override_path' => JPATH_THEMES.'/'.$template.'/html'
        ));

        parent::_initialize($config);
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

        /*
         * Theme path
         */
        if(!empty($this->_override_path))
        {
            //Remove the 'view' element from the path.
            $path = $info['path'];
            if(isset($path[0]) && $path[0] == 'view') {
                array_shift($path);
            }

            $path = count($path) ? implode('/', $path).'/' : '';

            //If no type exists create a glob pattern
            if(!empty($info['type'])) {
                $paths[] = $this->_override_path.'/mod_'.$package.'/'.$path.$info['file'].'.'.$info['format'].'.'.$info['type'];
            } else {
                $paths[] = $this->_override_path.'/mod_'.$package.'/'.$path.$info['file'].'.'.$info['format'].'.*';
            }

        }

        //Switch basepath
        if(!$locator->getNamespace(ucfirst($package))) {
            $basepath = $locator->getNamespace('\\');
        } else {
            $basepath = $locator->getNamespace(ucfirst($package));
        }

        $basepath .= '/mod_'.strtolower($package);

        $filepath   = (count($info['path']) ? implode('/', $info['path']).'/' : '').'tmpl/';

        //If no type exists create a glob pattern
        if(!empty($info['type'])) {
            $filepath  .= $info['file'].'.'.$info['format'].'.'.$info['type'];
        } else {
            $filepath  .= $info['file'].'.'.$info['format'].'.*';
        }

        $paths[] = $basepath.'/'.$filepath;

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