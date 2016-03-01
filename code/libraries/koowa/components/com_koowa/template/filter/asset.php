<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Url Template Filter
 *
 * Filter allows to create url schemes that are replaced on compile and render.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComKoowaTemplateFilterAsset extends KTemplateFilterAsset
{
    /**
     * Static cache of component asset paths
     *
     * @var array
     */
    protected static $_component_assets = array();

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $path       = rtrim($this->getObject('request')->getSiteUrl()->getPath(), '/');
        $nooku_path = $path.'/media/koowa/com_koowa';
        $schemes    = array(
            'assets://js'  => $nooku_path.'/js',
            'assets://css' => $nooku_path.'/css',
            'assets://img' => $nooku_path.'/img',
            'media://'     => $path.'/media/',
            'root://'      => $path.'/',
            'base://'      => rtrim($this->getObject('request')->getBaseUrl()->getPath(), '/').'/'
        );

        if (empty(static::$_component_assets))
        {
            $components = $this->getObject('object.bootstrapper')->getComponents();

            foreach($components as $component)
            {
                $identifier = $this->getIdentifier($component);

                // Only register reusable components such as files and koowa
                if ($identifier->domain === 'koowa')
                {
                    $key         = 'assets://'.$identifier->package;
                    $destination = $path.'/media/koowa/com_'.$identifier->package;

                    static::$_component_assets[$key] = $destination;
                }
            }
        }

        $schemes = array_merge($schemes, static::$_component_assets);

        // Override default scheme with the media/ folder
        $schemes['assets://'] = $path.'/media/';

        $config->append(array(
            'schemes' => $schemes,
        ));

        parent::_initialize($config);
    }
}