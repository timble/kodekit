<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Koowa Bootstrapping
 *
 * If KOOWA is defined assume it was already loaded and bootstrapped
 */
if(!defined('KOOWA'))
{
    /**
     * Joomla Configuration
     */
    require_once JPATH_CONFIGURATION . '/configuration.php';
    $config = new JConfig;

    /**
     * Joomla Version
     */
    require_once JPATH_LIBRARIES . '/cms/version/version.php';
    $version = new JVersion;

    /*
     * Joomla checks if mb_substr exists to determine the availability of mbstring extension
     * Loading JString here before bootstrapping Koowa makes sure our replacement function
     * in legacy.php does not break anything
     */
    if (!function_exists('mb_substr') && class_exists('JLoader') && is_callable(array('JLoader', 'import')))
    {
        JLoader::import('joomla.string.string');
        JLoader::load('JString');
    }

    /**
     * Framework Bootstrapping
     */
    require_once __DIR__.'/code/libraries/koowa/libraries/koowa.php';
    Koowa::getInstance(array(
        'debug'           => $config->debug,
        'cache'           => false, //JFactory::getApplication()->getCfg('caching')
        'cache_namespace' => 'koowa-' . JPATH_BASE === JPATH_SITE ? 'site' : 'admin' . '-' . md5($config->secret),
        'root_path'       => JPATH_ROOT,
        'base_path'       => JPATH_BASE,
        'vendor_path'     => false //Composer loader is already registered.
    ));

    /**
     * Component Bootstrapping
     */
    KObjectManager::getInstance()->getObject('object.bootstrapper')
        ->registerComponents(JPATH_LIBRARIES.'/koowa/components', 'koowa')
        ->registerApplication('site', JPATH_SITE . '/components', JFactory::getApplication()->isSite())
        ->registerApplication('admin', JPATH_ADMINISTRATOR . '/components', JFactory::getApplication()->isAdmin())
        ->bootstrap();
}