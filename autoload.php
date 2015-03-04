<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

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
    'vendor_path'     => JPATH_ROOT.(version_compare($version->getShortVersion(), '3.4', '>=') ? '/libraries/vendor' : '/vendor')
));

/**
 * Component Bootstrapping
 */
KObjectManager::getInstance()->getObject('object.bootstrapper')
    ->registerComponents(JPATH_LIBRARIES.'/koowa/components', 'koowa')
    ->bootstrap();