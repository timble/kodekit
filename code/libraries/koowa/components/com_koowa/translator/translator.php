<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTranslator extends KTranslator
{
    /**
     * Maps identifier types to words
     *
     * @var array
     */
    protected static $_type_map = array(
        'com' => 'component',
        'mod' => 'module',
        'plg' => 'plugin'
    );

    /**
     * @param KObjectConfig $config
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (!$this->isLoaded('koowa'))
        {
            $this->loadTranslations('com_koowa');
            $this->_loaded['koowa'] = true;
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'locale'  => JFactory::getConfig()->get('language'),
        ));

        parent::_initialize($config);
    }

    /**
     * Load the extension language files.
     *
     * @param string|KObjectIdentifier $extension Extension identifier or name (e.g. com_files)
     * @param string $app Application. Leave blank for current one.
     *
     * @return boolean
     */
    public function loadTranslations($extension, $app = null)
    {
        if ($extension instanceof KObjectIdentifier) {
            $extension = $extension->type.'_'.$extension->package;
        }

        $folder = $this->_getExtensionFolder($extension, $app);

        $results = array();
        $results[] = $this->_loadTranslation($extension, $this->getLocaleFallback(), $folder);

        if ($this->getLocale() !== $this->getLocaleFallback()) {
            $results[] = $this->_loadTranslation($extension, $this->getLocale(), $folder);
        }

        return in_array(true, $results);
    }

    /**
     * Gets the folder for an extension
     *
     * @throws BadMethodCallException
     *
     * @param string $extension Extension
     * @param string $app       Application. Leave blank for current one.
     * @return string
     */
    protected function _getExtensionFolder($extension, $app = null)
    {
        $type    = substr($extension, 0, 3);
        $package = substr($extension, 4);

        if ($override = $this->getObject('manager')->getClassLoader()->getLocator('component')->getNamespace(ucfirst($package))) {
            $base = $override;
        }
        else
        {
            switch ($app)
            {
                case 'admin':
                    $base = JPATH_ADMINISTRATOR;
                    break;
                case 'site':
                    $base = JPATH_SITE;
                    break;
                default:
                    $base = JPATH_BASE;
            }
        }

        if (isset(self::$_type_map[$type])) {
            $type_folder = self::$_type_map[$type];
        } else {
            throw new BadMethodCallException(sprintf('Invalid extension type: %s', $type));
        }

        if ($type == 'plg')
        {
            $parts = explode('_', $package);
            if (count($parts) != 2) {
                throw new BadMethodCallException(sprintf('Invalid plugin: %s', $extension));
            }

            $folder = sprintf('%s/%ss/%s/%s', JPATH_ROOT, $type_folder, $parts[0], $parts[1]);
        }
        else $folder = sprintf('%s/%ss/%s', $base, $type_folder, $extension);

        // Special case for Koowa components
        if (is_dir($folder.'/resources/language')) {
            $folder .= '/resources';
        }

        return $folder;
    }

    /**
     * Loads a Joomla language file
     *
     * @param string $extension
     * @param string $locale Locale name
     * @param string $base   Base path
     * @return bool
     */
    protected function _loadTranslation($extension, $locale, $base)
    {
        $result    = true;
        $signature = md5($extension.$base.$locale);

        if (!isset($this->_loaded[$signature]))
        {
            $result = JFactory::getLanguage()->load($extension, $base, $locale, true, false);

            if ($result) {
                $this->_loaded[$signature] = true;
            }
        }

        return $result;
    }
}
