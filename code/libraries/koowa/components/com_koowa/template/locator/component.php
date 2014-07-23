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
 * @package Koowa\Component\Koowa
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
        if(!empty($this->_theme_path))
        {
            //Remove the 'view' element from the path.
            $path = $info['path'];
            if(isset($path[0]) && $path[0] == 'view') {
                array_shift($path);
            }

            //Find the template file
            $filepath = $info['package'].'/'.implode('/', $path).'/'.$info['file'].'.'.$info['format'].'.php';

            if ($override = $this->realPath($this->_theme_path.'/tmpl/'.$filepath)) {
                return $override;
            }
        }

        return parent::find($info);
    }
}