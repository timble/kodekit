<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Listbox Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperListbox extends KTemplateHelperListbox
{
    /**
     * Provides a users select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @return string The autocomplete users select box.
     */
    public function users($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'        => 'users',
            'name'         => 'user',
            'value'        => 'id',
            'label'        => 'name',
            'sort'         => 'name',
            'validate'     => false
        ));

        return $this->_autocomplete($config);
    }

    /**
     * Generates an HTML access listbox
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function access($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name'      => 'access',
            'attribs'   => array(),
            'deselect_value' => '',
            'deselect'  => true,
            'prompt'    => '- '.$this->getObject('translator')->translate('Select').' -'
        ))->append(array(
            'selected'  => $config->{$config->name}
        ));

        $prompt = false;

        // without &nbsp; Joomla strips the last hyphen of the prompt
        if ($config->deselect)  {
            $prompt = array((object) array('value' => $config->deselect_value, 'text'  => $config->prompt.'&nbsp;'));
        }

        $html = JHtml::_('access.level', $config->name, $config->selected, $config->attribs->toArray(), $prompt);
    
        return $html;
    }
}
