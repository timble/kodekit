<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Template
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
abstract class ComKoowaTemplateAbstract extends KTemplateAbstract
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
	 * Prevent creating instances of this class by making the constructor private
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

	    if (JFactory::getConfig()->get('caching')) {
	        $this->_cache = JFactory::getCache('com_koowa.templates', 'output');
		}
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config  An optional KObjectConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'locators' => array('com' => 'com:koowa.template.locator.component')
        ));

        parent::_initialize($config);
    }

	/**
	 * This function tries to get the template from the cache. If it cannot be found
	 * the template file will be loaded from the file system.
	 *
	 * {@inheritdoc}
	 */
    public function load($path, $data = array(), $status = self::STATUS_LOADED)
    {
	    if(isset($this->_cache))
	    {
	        $identifier = md5($path);

	        if ($template = $this->_cache->get($identifier))
	        {
                //Push the path on the stack
                array_push($this->_stack, $path);

                $this->setContent($template, self::STATUS_COMPILED);

                //Compile and evaluate partial templates
                if(count($this->_stack) > 1)
                {
                    if(!($status & self::STATUS_COMPILED)) {
                        $this->compile();
                    }

                    if(!($status & self::STATUS_EVALUATED)) {
                        $this->evaluate($data);
                    }
                }

	            return $this;
	        }
	    }

		return parent::load($path, $data, $status);
	}

    /**
     * This function implements a caching mechanism when reading the template. If the template cannot be found in the
     * cache it will be filtered and stored in the cache. Otherwise it will be loaded from the cache and returned
     * directly.
     *
     * {@inheritdoc}
     */
    public function compile()
    {
        $result = parent::compile();

        if(isset($this->_cache) && $this->getPath())
        {
            $identifier = md5($this->getPath());

            //Store the object in the cache
            if (!$this->_cache->get($identifier)) {
                $this->_cache->store($this->_content, $identifier);
            }
        }

        return $result;
    }

    /**
     * Returns a directory path for temporary files
     *
     * Additionally checks for Joomla tmp folder if the system directory is not writable
     *
     * @throws RuntimeException If a temporary writable directory cannot be found
     * @return string Folder path
     */
    protected function _getTemporaryDirectory()
    {
        if (!self::$_temporary_directory)
        {
            $result     = false;
            $candidates = array(
                ini_get('upload_tmp_dir'),
                JFactory::getApplication()->getCfg('tmp_path'),
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
                throw new RuntimeException('Cannot find a writable temporary directory');
            }

            self::$_temporary_directory = $result;
        }

        return self::$_temporary_directory;
    }
}
