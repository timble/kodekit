<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Behavior Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperUi extends TemplateHelperAbstract implements TemplateHelperParameterizable
{
    /**
     * Loads the common UI libraries
     *
     * @param array $config
     * @return string
     */
    public function load($config = array())
    {
        $identifier = $this->getIdentifier();

        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug(),
            'package' => $identifier->package,
            'domain'  => $identifier->domain,
            'type'    => $identifier->type,
        ))->append(array(
            'k_ui_container' => ($config->domain === 'admin' || $config->domain === '') && $config->type === 'com',
            'styles' => array(
                'debug' => $config->debug,
                'package' => $config->package,
                'domain' => $config->domain,
                'decorator' => $config->decorator
            ),
        ))->append(array(
            'wrapper_class' => array(
                // Only add k-ui-container for top-level component templates
                ($config->k_ui_container ? 'k-ui-container'.($config->debug ? '' : ' k-no-css-errors') : ''),
                'k-ui-namespace',
                $identifier->type.'_'.$identifier->package
            ),
        ))->append(array(
            'wrapper' => sprintf('<div class="%s">
                <!--[if lte IE 8 ]><div class="old-ie"><![endif]-->
                %%s
                <!--[if lte IE 8 ]></div><![endif]-->
                </div>', implode(' ', ObjectConfig::unbox($config->wrapper_class))
            )
        ));

        $html = '';

        if ($config->styles !== false) {
            $html .= $this->styles($config->styles);
        }

        $html .= $this->scripts($config);

        if ($config->wrapper) {
            $html .= $this->wrapper($config);
        }

        return $html;
    }

    public function styles($config = array())
    {
        $identifier = $this->getIdentifier();

        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug(),
            'package' => $identifier->package,
            'domain'  => $identifier->domain
        ))->append(array(
            'folder' => $config->package,
            'file'   => $config->domain ?: 'admin'
        ));

        $html = '';

        if (empty($config->css_file) && $config->css_file !== false) {
            $config->css_file = sprintf('%scss/%s.css', (empty($config->folder) ? '' : $config->folder.'/'), $config->file);
        }

        if ($config->css_file) {
            $html .= '<ktml:style src="assets://'.$config->css_file.'" />';
        }

        return $html;
    }

    public function scripts($config = array())
    {
        $identifier = $this->getIdentifier();

        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug(),
            'domain'  => $identifier->domain
        ));

        $html = '';

        if (!TemplateHelperBehavior::isLoaded('k-js-enabled'))
        {
            $html .= '<script data-inline type="text/javascript">document.documentElement.classList.add(\'k-js-enabled\');</script>';

            TemplateHelperBehavior::setLoaded('k-js-enabled');
        }

        $html .= $this->createHelper('behavior')->modernizr($config);

        if (($config->domain === 'admin' || $config->domain === '')  && !TemplateHelperBehavior::isLoaded('admin.js')) {
            // Make sure jQuery is always loaded right before admin.js, helps when wrapping components
            TemplateHelperBehavior::setLoaded('jquery', false);

            $html .= $this->createHelper('behavior')->jquery($config);
            $html .= '<ktml:script src="assets://js/admin'.($config->debug ? '' : '.min').'.js" />';

            TemplateHelperBehavior::setLoaded('admin.js');
        }

        $html .= $this->createHelper('behavior')->kodekit($config);

        return $html;
    }

    public function wrapper($config = array())
    {
        $config = new ObjectConfigJson($config);

        return '<ktml:wrapper template="'.htmlentities($config->wrapper).'">';
    }
}