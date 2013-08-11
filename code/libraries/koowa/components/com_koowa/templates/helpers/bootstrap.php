<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Bootstrap Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperBootstrap extends ComKoowaTemplateHelperBehavior
{
    /**
     * Load Bootstrap JavaScript files, from Joomla if possible
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
                $html .= $this->jquery($config);
            }

            if (version_compare(JVERSION, '3.0', '>='))
            {
                JHtml::_('bootstrap.framework');
                self::$_loaded['bootstrap-javascript'] = true;
            }
            else {
                $html .= '<script src="media://koowa/com_koowa/js/bootstrap'.($config->debug ? '' : '.min').'.js" />';
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
            'debug'        => JFactory::getApplication()->getCfg('debug'),
            'javascript'   => false,
            'wrapper'      => $identifier->type.'_'.$identifier->package,
            'package'      => $identifier->package,
            'file'         => $identifier->type === 'mod' ? 'module' : $identifier->application,
            'load_default' => version_compare(JVERSION, '3.0', '<')
        ));

        $html = '';

        if ($config->javascript && !isset(self::$_loaded['bootstrap-javascript'])) {
            $html .= $this->javascript($config);
        }

        // Load the generic files
        // We assume that the template has either loaded Bootstrap or provided styles for it in 3.0+
        if ($config->load_default && !isset(self::$_loaded['bootstrap-css']))
        {
            $template = '<style src="media://koowa/com_koowa/css/bootstrap%s.css" />';
            $html    .= sprintf($template, $config->debug ? '' : '.min');

            self::$_loaded['bootstrap-css'] = true;
        }

        if (!isset(self::$_loaded[$config->package.'-'.$config->file]))
        {
            $template  = 'com_%s/css/%s.css';
            $try_files = array(
                sprintf($template, $config->package, $config->file)
            );

            if (version_compare(JVERSION, '3.0', '<')) {
                array_unshift($try_files, sprintf($template, $config->package, $config->file.'-25'));
            }

            foreach ($try_files as $file)
            {
                if (file_exists(JPATH_ROOT.'/media/'.$file))
                {
                    $html .= sprintf('<style src="media://%s" />', $file);

                    self::$_loaded[$config->package.'-'.$config->file] = true;

                    break;
                }
            }

        }

        if ($config->wrapper)
        {
            $this->wrapper(array(
                'wrapper' => sprintf('<div class="koowa %s">%%s</div>', $config->wrapper)
            ));
        }

        return $html;
    }

    /**
     * Wrap the output of the template with a filter
     *
     * @param array $config
     */
    public function wrapper($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'wrapper' => null
        ));

        if ($config->wrapper)
        {
            $this->getTemplate()->addFilter('wrapper');
            $this->getTemplate()->getFilter('wrapper')->setWrapper($config->wrapper);
        }
    }
}
