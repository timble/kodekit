<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Bootstrapper
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Component\Files
 */
class ComKoowaBootstrapper extends KObjectBootstrapperComponent
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
            'aliases'  => array(
                'request'                       => 'com:koowa.dispatcher.request',
                'lib:database.adapter.mysqli'   => 'com:koowa.database.adapter.mysqli',
                'translator'                    => 'com:koowa.translator',
                'user'                          => 'com:koowa.user',
                'filter.factory'                => 'com:koowa.filter.factory',
                'exception.handler'             => 'com:koowa.exception.handler',
                'date'                          => 'com:koowa.date',
                'event.publisher'               => 'com:koowa.event.publisher',
                'user.provider'                 => 'com:koowa.user.provider'
            ),
        ));

        parent::_initialize($config);
    }

    public function bootstrap()
    {
        $chain = $this->getObject('lib:object.bootstrapper.chain');

        //Framework components
        $directory = JPATH_LIBRARIES.'/koowa';
        foreach ($this->getComponents($directory) as $component)
        {
            //Register the namespace
            $this->getClassLoader()->getLocator('component')->registerNamespace(ucfirst($component), $directory);

            if($bootstrapper = $this->getBootstrapper($component)) {
                $chain->addBootstrapper($bootstrapper);
            }
        }

        //Application components
        $directory = JPATH_BASE;
        foreach ($this->getComponents($directory) as $component)
        {
            if (!file_exists($directory.'/components/com_'.$component.'/bootstrapper.php')) {
                continue;
            }

            if($bootstrapper = $this->getBootstrapper($component, false)) {
                $chain->addBootstrapper($bootstrapper);
            }
        }

        $chain->bootstrap();

        parent::bootstrap();
    }

    public function getBootstrapper($name, $fallback = true)
    {
        $bootstrapper = null;

        if(JFactory::getApplication()->isAdmin()) {
            $application = 'admin';
        } else {
            $application = 'site';
        }

        //Register the bootstrapper
        $identifier = 'com://'.$application.'/'.$name.'.bootstrapper';
        if($this->getObjectManager()->getClass($identifier, $fallback)) {
            $bootstrapper = $this->getObject($identifier);
        }

        return $bootstrapper;
    }

    public function getComponents($directory)
    {
        $components = array();
        foreach (new DirectoryIterator($directory.'/components') as $dir)
        {
            //Only get the component directory names
            if ($dir->isDot() || !$dir->isDir() || !preg_match('/^[a-zA-Z]+/', $dir->getBasename())) {
                continue;
            }

            $components[] = substr($dir, 4);
        }

        return $components;
    }

    public function getHandle()
    {
        //Prevent recursive bootstrapping
        return null;
    }
}