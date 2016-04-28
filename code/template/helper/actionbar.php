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
class TemplateHelperActionbar extends TemplateHelperAbstract
{
    /**
     * Render the action bar
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array(), TemplateInterface $template)
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'toolbar' => null,
            'attribs' => array('class' => array('koowa-toolbar'))
        ));

        $html = '';
        if(isset($config->toolbar))
        {
            //Force the id
            $config->attribs['id'] = 'toolbar-'.$config->toolbar->getType();

            $html  = '<div '.$this->buildAttributes($config->attribs).'>';
            $html .= '<div class="button__group">';
            foreach ($config->toolbar as $command)
            {
                $name = $command->getName();

                if(method_exists($this, $name)) {
                    $html .= $this->$name(ObjectConfig::unbox($command), $template);
                } else {
                    $html .= $this->command(ObjectConfig::unbox($command), $template);
                }
            }
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Render a action bar command
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array(), TemplateInterface $template)
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'id'      => '',
            'href'    => '',
            'allowed' => true,
            'disabled'=> false,
            'data'    => array(),
            'attribs' => array(
                'href'  => '#',
                'class' => array('button', 'toolbar')
            )
        ));

        $translator = $this->getObject('translator');

        if ($config->allowed === false)
        {
            $config->attribs->title = $translator->translate('You are not allowed to perform this action');
            $config->attribs->class->append(array('disabled', 'unauthorized'));
        }

        //Create the id
        $config->attribs['id'] = 'command-'.$config->id;

        //Add a disabled class if the command is disabled
        if($config->disabled) {
            $config->attribs->class->append(array('nolink'));
        }

        //Add the data attributes
        foreach($config->data as $key => $value) {
            $config->attribs['data-'.$key] = (string) $value;
        }

        //Create the href
        if(!empty($config->href)) {
            $config->attribs['href'] = $template->route($config->href);
        }

        $html  = '<a '.$this->buildAttributes($config->attribs).'>';
        $html .= ucfirst($translator->translate($config->label));
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
            'attribs' => array('class' => array('button__group'))
        ));

        $html = '</div><div '.$this->buildAttributes($config->attribs).'>';

        return $html;
    }

    /**
     * Render a dialog button
     *
     * @param array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function dialog($config = array(), TemplateInterface $template)
    {
        $html  = $this->createHelper('behavior')->modal();
        $html .= $this->command($config, $template);

        return $html;
    }
}
