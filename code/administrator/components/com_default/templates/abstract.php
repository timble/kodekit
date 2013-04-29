<?php
/**
 * @version     $Id: default.php 3655 2011-06-27 20:35:22Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Abstract Template
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
abstract class ComDefaultTemplateAbstract extends KTemplateAbstract
{
    /**
     * Temporary directory
     */
    protected static $_temporary_directory;

    /**
     * The cache object
     *
     * @var	JCache
     */
    protected $_cache;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the contructor private
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $caching = version_compare(JVERSION, '3.0', 'ge')
            ? JFactory::getConfig()->get('caching')
            : JFactory::getConfig()->getValue('config.caching');

        if($caching) {
            $this->_cache = JFactory::getCache('template', 'output');
        }
    }

    /**
     * Load a template by path -- first look in the templates folder for an override
     *
     * This function tries to get the template from the cache. If it cannot be found
     * the template file will be loaded from the file system.
     *
     * @param   string 	The template path
     * @param	array	An associative array of data to be extracted in local template scope
     * @return KTemplateAbstract
     */
    public function loadFile($path, $data = array(), $process = true)
    {
        if(isset($this->_cache))
        {
            $identifier = md5($path);

            if ($template = $this->_cache->get($identifier))
            {
                // store the path
                $this->_path = $path;

                $this->loadString($template, $data, $process);
                return $this;
            }
        }

        return parent::loadFile($path, $data, $process);;
    }

    /**
     * Searches for the file
     *
     * This function first tries to find a template override, if no override exists
     * it will try to find the default template
     *
     * @param	string	The file path to look for.
     * @return	mixed	The full path and file name for the target file, or FALSE
     * 					if the file is not found
     */
    public function findFile($path)
    {
        $template  = JFactory::getApplication()->getTemplate();
        $override  = JPATH_THEMES.'/'.$template.'/html';
        $override .= str_replace(array(JPATH_BASE.'/modules', JPATH_BASE.'/components', '/views'), '', $path);

        //Try to load the template override
        $result = parent::findFile($override);

        if($result === false)
        {
            //If the path doesn't contain the /tmpl/ folder add it
            if(strpos($path, '/tmpl/') === false) {
                $path = dirname($path).'/tmpl/'.basename($path);
            }

            $result = parent::findFile($path);
        }

        return $result;
    }

    /**
     * Parse the template
     *
     * This function implements a caching mechanism when reading the template. If the template cannot be found in the
     * cache it will be filtered and stored in the cache. Otherwise it will be loaded from the cache and returned
     * directly.
     *
     * @param string The template content to parse
     * @return void
     */
    protected function _parse(&$content)
    {
        if(isset($this->_cache))
        {
            $identifier = md5($this->getPath());

            if (!$this->_cache->get($identifier))
            {
                parent::_parse($content);

                //Store the object in the cache
                $this->_cache->store($content, $identifier);
            }
            else $content = $this->_cache->get($identifier);
        }
        else parent::_parse($content);
    }

    /**
     * Returns a directory path for temporary files
     *
     * Additionally checks for Joomla tmp folder if the system directory is not writable
     *
     * @throws KTemplateException
     * @return string Folder path
     */
    protected function _getTemporaryDirectory()
    {
        if (!self::$_temporary_directory)
        {
            $result     = false;
            $candidates = array(
                ini_get('upload_tmp_dir'),
                JPATH_ROOT.'/tmp'
            );

            if (function_exists('sys_get_temp_dir')) {
                array_unshift($candidates, sys_get_temp_dir());
            }

            foreach ($candidates as $folder)
            {
                if ($folder && @is_dir($folder) && is_writable($folder))
                {
                    $result = rtrim($folder, '\\/');
                    break;
                }
            }

            if ($result === false) {
                throw new KTemplateException('Cannot find a writable temporary directory');
            }

            self::$_temporary_directory = $result;
        }

        return self::$_temporary_directory;
    }
}