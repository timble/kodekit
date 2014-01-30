<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

/**
 * Bootstrapper
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Files
 */
class ComKoowaBootstrapper extends KObjectBootstrapperComponent
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
            'aliases'  => array(
                'request'                       => 'lib:dispatcher.request',
                'lib:database.adapter.mysqli'   => 'com:koowa.database.adapter.mysqli',
                'translator'                    => 'com:koowa.translator',
                'user'                          => 'com:koowa.user',
                'exception.handler'             => 'com:koowa.exception.handler',
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