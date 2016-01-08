<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * File Override Locator
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Locator
 */
class ComKoowaTemplateLocatorFile extends KTemplateLocatorFile
{
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
     * Find a template override
     *
     * @param array  $info      The path information
     * @return bool|mixed
     */
    public function find(array $info)
    {
        if(!empty($this->_override_path))
        {
            //Qualify partial templates.
            if(dirname($info['url']) === '.')
            {
                if(empty($info['base'])) {
                    throw new RuntimeException('Cannot qualify partial template path');
                }

                $path = dirname($info['base']);
            }
            else $path = dirname($info['url']);

            $file   = pathinfo($info['url'], PATHINFO_FILENAME);
            $format = pathinfo($info['url'], PATHINFO_EXTENSION);
            $path   = $this->_override_path.'/'.str_replace(parse_url($path, PHP_URL_SCHEME).'://', '', $path);

            // Remove /view from the end of the override path
            if (substr($path, strrpos($path, '/')) === '/view') {
                $path = substr($path, 0, -5);
            }

            if(!$result = $this->realPath($path.'/'.$file.'.'.$format))
            {
                $pattern = $path.'/'.$file.'.'.$format.'.*';
                $results = glob($pattern);

                //Try to find the file
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
        }

        return parent::find($info);
    }
}