<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Application Bootstrapper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaObjectBootstrapperApplication extends KObjectBootstrapperChain
{
    /**
     * The application root directory
     *
     * @var string
     */
    protected $_directory;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_directory = $config->directory;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'directory' => JPATH_BASE.'/components',
        ));

        parent::_initialize($config);
    }

    /**
     * Bootstrap the application
     *
     * @return void
     */
    public function bootstrap()
    {
        foreach (new DirectoryIterator($this->_directory) as $dir)
        {
            //Only get the component directory names
            if ($dir->isDot() || !$dir->isDir() || !preg_match('/^[a-zA-Z]+/', $dir->getBasename())) {
                continue;
            }

            if(file_exists($dir->getRealPath().'/bootstrapper.php'))
            {
                $bootstrapper = $this->getObject('com:'.substr($dir, 4).'.bootstrapper');
                $this->addBootstrapper($bootstrapper);
            }
        }

        parent::bootstrap();
    }
}