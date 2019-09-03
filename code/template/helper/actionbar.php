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
 * Action bar Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperActionbar extends TemplateHelperToolbar
{
    /**
     * Render the action bar
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'toolbar' => null,
            'attribs' => array('class' => array('k-toolbar', 'k-js-toolbar'))
        ));

        $html = '';

        if(isset($config->toolbar) && count($config->toolbar))
        {
            //Force the id
            $config->attribs['id'] = 'toolbar-'.$config->toolbar->getType();

            foreach ($config->toolbar as $command)
            {
                $name = $command->getName();

                if(method_exists($this, $name)) {
                    $html .= $this->$name(ObjectConfig::unbox($command));
                } else {
                    $html .= $this->command(ObjectConfig::unbox($command));
                }
            }

            if (!empty($html)) {
                $html = '<div '.$this->buildAttributes($config->attribs).'>'.$html.'</div>';
            }
        }

        return $html;
    }

    /**
     * Render a action bar command
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'id'      => '',
            'href'    => '',
            'icon'    => '',
            'allowed' => true,
            'disabled'=> false,
            'data'    => array(),
            'attribs' => array(
                'href'  => '#',
                'class' => array('toolbar')
            )
        ));

        $translator = $this->getObject('translator');

        if ($config->allowed === false)
        {
            $config->attribs->title = $translator->translate('You are not allowed to perform this action');
            $config->attribs->class->append(array('k-is-disabled', 'k-is-unauthorized'));
        }

        //Create the id
        $config->attribs['id'] = 'command-'.$config->id;

        $config->attribs->class->append(array('k-button', 'k-button--default', 'k-button-'.$config->id));

        if ($config->id === 'new' || $config->id === 'save') {
            $config->attribs->class->append(array('k-button--success'));
        }

        //Add the data attributes
        foreach($config->data as $key => $value) {
            $config->attribs['data-'.$key] = (string) $value;
        }

        //Create the href
        if(!empty($config->href)) {
            $config->attribs['href'] = $config->href;
        }

        $attribs = clone $config->attribs;
        $attribs->class = implode(" ", ObjectConfig::unbox($attribs->class));

        $html = '<a '.$this->buildAttributes($attribs).'>';

        $html .= '<span class="'.$config->icon.'" aria-hidden="true"></span> ';
        $html .= '<span class="k-button__text">';
        $html .= $translator->translate($config->label);
        $html .= '</span>';
        $html .= '</a>';

        return $html;
    }

    /**
     * Render a separator
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function separator($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'attribs' => array()
        ));

        return '';
    }

    /**
     * Render a dialog button
     *
     * @param array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function dialog($config = array())
    {
        $html  = $this->createHelper('behavior')->modal();
        $html .= $this->command($config);

        return $html;
    }
}
