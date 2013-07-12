<?php
/**
 * @version     $Id$
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Behavior Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaTemplateHelperBootstrap extends KTemplateHelperBootstrap
{
    /**
     * Load Bootstrap JavaScript files from Joomla if possible
     *
     * @param array|KConfig $config
     * @return string
     */
    public function javascript($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));
        $html   = '';

        if (!isset(self::$_loaded['bootstrap-javascript']))
        {
            if (!isset(self::$_loaded['jquery'])) {
                $html .= $this->getTemplate()->getHelper('behavior')->jquery($config);
            }

            if (version_compare(JVERSION, '3.0', '>='))
            {
                JHtml::_('bootstrap.framework');
                self::$_loaded['bootstrap-javascript'] = true;
            }
            else {
                $html .= parent::javascript($config);
            }
        }

        return $html;
    }

    /**
     * Loads necessary Bootstrap files
     *
     * @param array|KConfig $config
     *
     * @return string
     */
    public function load($config = array())
    {
        $identifier = $this->getTemplate()->getIdentifier();

        $config = new KConfig($config);
        $config->append(array(
            'debug'       => JFactory::getApplication()->getCfg('debug'),
            'javascript'  => false,
            'wrapper'     => sprintf('<div class="%s_%s">%%s</div>', $identifier->type, $identifier->package),
            'package'     => $identifier->package,
            'application' => $identifier->application
        ));

        $html = '';

        if ($config->javascript && !isset(self::$_loaded['bootstrap-javascript'])) {
            $html .= $this->javascript($config);
        }

        // Load the generic files
        if (empty($config->package))
        {
            if (version_compare(JVERSION, '3.0', 'ge')) {
                JHtml::_('bootstrap.loadCss');
            } else {
                $html .= parent::load($config);
            }
        }
        else
        {
            $filename = 'bootstrap'.($config->application ? '-'.$config->application : '');
            if (version_compare(JVERSION, '3.0', 'ge')) {
                $filename .= 'j3';
            }

            if (!isset(self::$_loaded[$config->package.'-'.$filename]))
            {
                // Load the base bootstrap file too
                if (substr($filename, -2) !== 'j3') {
                    $html .= '<style src="media://com_'.$config->package.'/bootstrap/css/bootstrap.css" />';
                }

                $html .= '<style src="media://com_'.$config->package.'/bootstrap/css/'.$filename.'.css" />';

                self::$_loaded[$config->package.'-'.$filename] = true;
            }
        }

        if ($config->wrapper) {
            $this->wrapper(array('wrapper' => $config->wrapper));
        }

        return $html;
    }
}