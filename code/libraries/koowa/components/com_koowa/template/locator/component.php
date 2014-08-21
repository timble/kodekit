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
     * The theme path
     *
     * @var string
     */
    protected $_theme_path;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_theme_path = $config->theme_path;
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
            'theme_path' => JPATH_THEMES.'/'.$template.'/html'
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
        $paths  = array();
        $loader = $this->getObject('manager')->getClassLoader();

        //Get the package
        $package = $info['package'];

        //Get the domain
        $domain = $info['domain'];

        /*
         * Theme path
         */
        if(!empty($this->_theme_path))
        {
            //Remove the 'view' element from the path.
            $path = $info['path'];
            if(isset($path[0]) && $path[0] == 'view') {
                array_shift($path);
            }

            //Find the template file
            $paths[] = $this->_theme_path.'/com_'.$package.'/'.implode('/', $path).'/'.$info['file'].'.'.$info['format'].'.php';
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

        //View folder
        $paths[] = $basepath.'/view/'.implode('/', $info['path']).'/tmpl/'.$info['file'].'.'.$info['format'].'.php';

        //Views folder
        $paths[] = $basepath.'/views/'.implode('/', $info['path']).'/tmpl/'.$info['file'].'.'.$info['format'].'.php';

        foreach($paths as $path)
        {
            if($result = $this->realPath($path)) {
                return $result;
            }
        }

        return false;
    }
}