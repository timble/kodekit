<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Bootstrap Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperBootstrap extends ComKoowaTemplateHelperBehavior
{
    /**
     * Load Bootstrap JavaScript files, from Joomla if possible
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function javascript($config = array())
    {
        return $this->bootstrap(array('css' => false, 'javascript' => true));
    }

    /**
     * Loads necessary Bootstrap files
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function load($config = array())
    {
        $identifier = $this->getTemplate()->getIdentifier();

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'          => JFactory::getApplication()->getCfg('debug'),
            'javascript'     => false,
            'package'        => $identifier->package,
            'file'           => $identifier->type === 'mod' ? 'module' : $identifier->domain,
            'load_base'      => version_compare(JVERSION, '3.0', '<'),
            'class'          => array(
                'koowa',
                $identifier->type.'_'.$identifier->package,
                JFactory::getLanguage()->isRTL() ? 'koowa--rtl' : '',
            ),
        ))->append(array(
            'wrapper' => sprintf('<div class="%s">
                <!--[if lte IE 8 ]><div class="old-ie"><![endif]-->
                %%s
                <!--[if lte IE 8 ]></div><![endif]-->
                </div>', implode(' ', KObjectConfig::unbox($config->class))
            )
        ));

        $html = '';

        if ($config->javascript)
        {
            $config->css = false;
            $html .= $this->javascript($config);
            $config->css = true;
        }

        // Load the generic files
        // We assume that the template has either loaded Bootstrap or provided styles for it in 3.0+
        if (!isset(self::$_loaded['bootstrap-css']))
        {
            $template = JPATH_THEMES.'/'.JFactory::getApplication()->getTemplate();

            if ($config->load_base)
            {
                if (!file_exists($template.'/disable-koowa-bootstrap.txt')) {
                    $html .= parent::bootstrap($config);
                }
            }
            else
            {
                if (file_exists($template.'/enable-koowa-bootstrap.txt')) {
                    $html .= parent::bootstrap($config);
                }
            }

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
                    $html .= sprintf('<ktml:style src="media://%s" />', $file);

                    self::$_loaded[$config->package.'-'.$config->file] = true;
                    break;
                }
            }
        }

        if ($config->wrapper)
        {
            $this->getTemplate()->addFilter('wrapper');
            $this->getTemplate()->getFilter('wrapper')->setWrapper($config->wrapper);
        }

        return $html;
    }
}
