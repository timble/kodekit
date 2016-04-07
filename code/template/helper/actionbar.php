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
     * @param 	array 	$config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
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
                    $html .= $this->$name(array('command' => $command));
                } else {
                    $html .= $this->command(array('command' => $command));
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
     * @param 	array 	$config An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'command' => array('attribs' => array('class' => array('button', 'toolbar')))
        ));

        $translator = $this->getObject('translator');
        $command = $config->command;

        if ($command->allowed === false)
        {
            $command->attribs->title = $translator->translate('You are not allowed to perform this action');
            $command->attribs->class->append(array('disabled', 'unauthorized'));
        }

        //Create the id
        $command->attribs['id'] = 'command-'.$command->id;

        //Add a disabled class if the command is disabled
        if($command->disabled) {
            $command->attribs->class->append(array('nolink'));
        }

        //Create the href
        $command->attribs->append(array('href' => '#'));
        if(!empty($command->href)) {
            $command->attribs['href'] = $this->getTemplate()->route($command->href);
        }

        $html  = '<a '.$this->buildAttributes($command->attribs).'>';
        $html .= ucfirst($translator->translate($command->label));
        $html .= '</a>';

        return $html;
    }

    /**
     * Render a separator
     *
     * @param 	array 	$config An optional array with configuration options
     * @return  string  Html
     */
    public function separator($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'command' => array('attribs' => array('class' => array('button__group')))
        ));

        $command = $config->command;

        $html = '</div><div '.$this->buildAttributes($command->attribs).'>';

        return $html;
    }

    /**
     * Render a dialog button
     *
     * @param 	array 	$config An optional array with configuration options
     * @return  string  Html
     */
    public function dialog($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'command' => NULL
        ));

        $html  = $this->getTemplate()->helper('behavior.modal');
        $html .= $this->command($config);

        return $html;
    }
}
