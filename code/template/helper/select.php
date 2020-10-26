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
 * Select Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperSelect extends TemplateHelperAbstract implements TemplateHelperParameterizable
{
    /**
     * Generates an HTML select option
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function option($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'id'        => null,
            'name'      => 'id',
            'value'     => null,
            'label'     => '',
            'disabled'  => false,
            'level'     => 1,
            'attribs'   => array(),
        ));

        $option = new \stdClass;
        $option->id       = $config->id;
        $option->name     = $config->name;
        $option->value 	  = $config->value;
        $option->label    = trim( $config->label ) ? $config->label : $config->value;
        $option->disabled = $config->disabled;
        $option->level    = $config->level;
        $option->attribs  = ObjectConfig::unbox($config->attribs);

        if($config->level) {
            $option->attribs['class'] = array('level'.$config->level);
        }

        if($config->disabled) {
            $option->attribs['class'] = array('k-is-disabled', 'disabled');
        }

        return $option;
    }

    /**
     * Generates a select option list
     *
     * @param   array   $config An optional array with configuration options
     * @return  array   An array of objects containing the option attributes
     */
    public function options( $config = array() )
    {
        $config = new ObjectConfig($config);
        $config->append(array(
            'entity'    => array(),
            'name'      => 'id',
            'value'     => 'id',
            'label'     => 'id',
            'disabled'  => null,
            'attribs'   => array(),
        ));
        $options = array();
        foreach($config->entity as $entity)
        {
            $option = array(
                'id'       => isset($entity->{$config->name}) ? $entity->{$config->name} : null,
                'name'     => $config->name,
                'disabled' => $config->disabled,
                'attribs'  => ObjectConfig::unbox($config->attribs),
                'value'    => $entity->{$config->value},
                'label'    => $entity->{$config->label},
            );
            if($config->entity instanceof \RecursiveIteratorIterator) {
                $option['level'] = $config->entity->getDepth() + 1;
            }
            $options[] = $this->option($option);
        }
        return $options;
    }

    /**
     * Generates an HTML select list
     *
     * @param   array|ObjectConfig     $config An optional array with configuration options
     * @return  string  Html
     */
    public function optionlist($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'options'   => array(),
            'name'      => 'id',
            'selected'  => null,
            'disabled'  => null,
            'translate' => false,
            'attribs'   => array('size' => 1),
        ));

        $config->attribs['name'] = $config->name;

        $translator = $this->getObject('translator');

        $html = [];
        $groupHtml = [];

        foreach($config->options as $group => $options)
        {
            if (is_numeric($group)) {
                $options = array($options);
            }

            foreach ($options as $option)
            {
                $optionAttribs = isset($option->attribs) ? $option->attribs : [];
                $value = $option->value;
                $label = $config->translate ? $translator->translate( $option->label ) : $option->label;

                if(isset($option->disabled) && $option->disabled) {
                    $optionAttribs['disabled'] = true;
                }

                if(!is_null($config->selected))
                {
                    if ($config->selected instanceof ObjectConfig)
                    {
                        foreach ($config->selected as $selected)
                        {
                            $sel = is_object($selected) ? $selected->value : $selected;
                            if ((string) $value == (string) $sel)
                            {
                                $optionAttribs['selected'] = true;
                                break;
                            }
                        }
                    }
                    elseif ((string) $value == (string) $config->selected) {
                        $optionAttribs['selected'] = true;
                    }
                }

                $optionAttribs['value'] = $value;

                $groupHtml[] = $this->buildElement('option', $optionAttribs, $label);
            }

            if (!is_numeric($group)) {
                $html[] = $this->buildElement('optgroup', ['label' => StringEscaper::attr($group)], $groupHtml);
                $groupHtml = [];
            }
        }

        $html = array_merge($html, $groupHtml);

        return $this->buildElement('select', $config->attribs, $html);
    }

    /**
     * Generates an HTML radio list
     *
     * @param   array|ObjectConfig     $config An optional array with configuration options
     * @return  string  Html
     */
    public function radiolist($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'options' 	=> array(),
            'legend'    => null,
            'name'   	=> 'id',
            'selected'	=> null,
            'translate'	=> false,
            'attribs'	=> array(),
        ));

        $config->attribs['name'] = $config->name;

        $translator = $this->getObject('translator');

        $html = [];

        if(isset($config->legend)) {
            $legend = $config->translate ? $translator->translate( $config->legend ) : $config->legend;
            $html[] = $this->buildElement('legend', [], $legend);
        }

        foreach($config->options as $option)
        {
            $value = $option->value;
            $optionAttribs = isset($option->attribs) ? $option->attribs : [];

            if ($value == $config->selected) {
                $optionAttribs['checked'] = true;
            }

            if(isset($option->disabled) && $option->disabled) {
                $optionAttribs['disabled'] = true;
            }

            $optionAttribs['type'] = 'radio';
            $optionAttribs['name'] = $config->name.'[]';
            $optionAttribs['id'] = $config->name.$option->id;
            $optionAttribs['value'] = $value;

            $html[] = $this->buildElement('label', ['class' => 'radio', 'for' => $option->name.$option->id],
                $this->buildElement('input', $optionAttribs)
                . $config->translate ? $translator->translate( $option->label ) : $option->label
            );
        }

        return $this->buildElement('fieldset', $config->attribs, $html);
    }

    /**
     * Generates an HTML check list
     *
     * @param   array|ObjectConfig     $config An optional array with configuration options
     * @return  string	Html
     */
    public function checklist( $config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'options' 	=> array(),
            'legend'    => null,
            'name'   	=> 'id',
            'selected'	=> null,
            'translate'	=> false,
            'attribs'	=> array(),
        ));

        $config->attribs['name'] = $config->name;

        $translator = $this->getObject('translator');

        $html = [];

        if(isset($config->legend)) {
            $legend = $config->translate ? $translator->translate( $config->legend ) : $config->legend;
            $html[] = $this->buildElement('legend', [], $legend);
        }

        foreach($config->options as $option)
        {
            $value = $option->value;
            $optionAttribs = isset($option->attribs) ? $option->attribs : [];

            if ($config->selected instanceof ObjectConfig)
            {
                foreach ($config->selected as $selected)
                {
                    $selected = is_object( $selected ) ? $selected->{$config->value} : $selected;
                    if ($value == $selected)
                    {
                        $optionAttribs['checked'] = true;
                        break;
                    }
                }
            }
            elseif ($value == $config->selected) {
                $optionAttribs['checked'] = true;
            }

            if(isset($option->disabled) && $option->disabled) {
                $optionAttribs['disabled'] = true;
            }

            $optionAttribs['type'] = 'checkbox';
            $optionAttribs['name'] = $config->name.'[]';
            $optionAttribs['id'] = $config->name.$option->id;
            $optionAttribs['value'] = $value;

            $html[] = $this->buildElement('label', ['class' => 'checkbox', 'for' => $config->name.$option->id],
                $this->buildElement('input', $optionAttribs)
                . $config->translate ? $translator->translate( $option->label ) : $option->label
            );
        }

        return $this->buildElement('fieldset', $config->attribs, $html);
    }

	/**
	 * Generates an HTML boolean radio list
	 *
	 * @param   array|ObjectConfig     $config An optional array with configuration options
	 * @return  string  Html
	 */
    public function booleanlist($config = array())
    {
        $translator = $this->getObject('translator');

        $config = new ObjectConfigJson($config);
        $config->append(array(
            'name'   	=> '',
            'attribs'	=> array(),
            'true'		=> $translator->translate('Yes'),
            'false'		=> $translator->translate('No'),
            'selected'	=> null,
            'translate'	=> true
        ));

        $name    = $config->name;
        $attribs = ObjectConfig::unbox($config->attribs);
        $text1   = $config->translate ? $translator->translate( $config->true ) : $config->true;
        $text0   = $config->translate ? $translator->translate( $config->false ) : $config->false;

        $input_attribs = array_merge(['type' => 'radio', 'name' => $name], $attribs);

        $input1 = $this->buildElement('input', array_merge($input_attribs, [
            'id' => $name.'1', 'value' => '1', 'checked' => $config->selected
        ]));
        $input0 = $this->buildElement('input', array_merge($input_attribs, [
            'id' => $name.'0', 'value' => '0', 'checked' => !$config->selected
        ]));

        return $this->buildElement('div', ['class' => 'k-optionlist k-optionlist--boolean'],
            $this->buildElement('div', ['class' => 'k-optionlist__content'],
                $input1
                . $this->buildElement('label', ['for' => $name.'1'], $this->buildElement('span', [], $text1))
                . $input0
                . $this->buildElement('label', ['for' => $name.'0'], $this->buildElement('span', [], $text0))
                . $this->buildElement('div', ['class' => 'k-optionlist__focus'])
            )
        );
    }
}
