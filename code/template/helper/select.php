<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Select Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Helper
 */
class KTemplateHelperSelect extends KTemplateHelperAbstract implements KTemplateHelperParameterizable
{
    /**
     * Generates an HTML select option
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function option($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'id'        => null,
            'name'      => 'id',
            'value'     => null,
            'label'     => '',
            'disabled'  => false,
            'level'     => 1,
            'attribs'   => array(),
        ));

        $option = new stdClass;
        $option->id       = $config->id;
        $option->name     = $config->name;
        $option->value 	  = $config->value;
        $option->label    = trim( $config->label ) ? $config->label : $config->value;
        $option->disabled = $config->disabled;
        $option->level    = $config->level;
        $option->attribs  = KObjectConfig::unbox($config->attribs);

        if($config->level) {
            $option->attribs['class'] = array('level'.$config->level);
        }

        if($config->disabled) {
            $option->attribs['class'] = array('disabled');
        }

        return $option;
    }

    /**
     * Generates an HTML select list
     *
     * @param   array|KObjectConfig     $config An optional array with configuration options
     * @return  string  Html
     */
    public function optionlist($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'options'   => array(),
            'name'      => 'id',
            'selected'  => null,
            'disabled'  => null,
            'translate' => false,
            'attribs'   => array('size' => 1),
        ));

        $html = array();

        $html[] = '<select name="'. $config->name .'" '. $this->buildAttributes($config->attribs) .'>';

        foreach($config->options as $group => $options)
        {
            if (is_numeric($group)) {
                $options = array($options);
            } else {
                $html[] = '<optgroup label="' . $this->getTemplate()->escape($group) . '">';
            }

            foreach ($options as $option)
            {
                $value = $option->value;
                $label = $config->translate ? $this->getObject('translator')->translate( $option->label ) : $option->label;

                $extra = '';
                if(isset($option->disabled) && $option->disabled) {
                    $extra .= 'disabled="disabled"';
                }

                if(isset($option->attribs)) {
                    $extra .= ' '.$this->buildAttributes($option->attribs);
                }

                if(!is_null($config->selected))
                {
                    if ($config->selected instanceof KObjectConfig)
                    {
                        foreach ($config->selected as $selected)
                        {
                            $sel = is_object($selected) ? $selected->value : $selected;
                            if ((string) $value == (string) $sel)
                            {
                                $extra .= 'selected="selected"';
                                break;
                            }
                        }
                    }
                    else $extra .= ((string) $value == (string) $config->selected ? ' selected="selected"' : '');
                }

                $html[] = '<option value="'. $value .'" '.$extra.'>' . $label . '</option>';
            }

            if (!is_numeric($group)) {
                $html[] = '</optgroup>';
            }
        }

        $html[] = '</select>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Generates an HTML radio list
     *
     * @param   array|KObjectConfig     $config An optional array with configuration options
     * @return  string  Html
     */
    public function radiolist($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'options' 	=> array(),
            'legend'    => null,
            'name'   	=> 'id',
            'selected'	=> null,
            'translate'	=> false,
            'attribs'	=> array(),
        ));

        $translator = $this->getObject('translator');
        $attribs    = $this->buildAttributes($config->attribs);

        $html   = array();
        $html[] = '<fieldset  name="'. $config->name .'" '. $attribs .'>';

        if(isset($config->legend)) {
            $html[] = '<legend>'.$config->translate ? $translator->translate( $config->legend ) : $config->legend.'</legend>';
        }

        foreach($config->options as $option)
        {
            $value = $option->value;
            $label = $config->translate ? $translator->translate( $option->label ) : $option->label;

            $extra = ($value == $config->selected ? 'checked="checked"' : '');

            if(isset($option->disabled) && $option->disabled) {
                $extra .= 'disabled="disabled"';
            }

            if(isset($option->attribs)) {
                $attribs = $this->buildAttributes($option->attribs);
            }

            $html[] = '<label class="radio" for="'.$config->name.$option->id.'">';
            $html[] = '<input type="radio" name="'.$config->name.'" id="'.$config->name.$option->id.'" value="'.$value.'" '.$extra.' '.$attribs.' />';
            $html[] = $label;
            $html[] = '</label>';
        }

        $html[] = '</fieldset>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Generates an HTML check list
     *
     * @param   array|KObjectConfig     $config An optional array with configuration options
     * @return  string	Html
     */
    public function checklist( $config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'options' 	=> array(),
            'legend'    => null,
            'name'   	=> 'id',
            'selected'	=> null,
            'translate'	=> false,
            'attribs'	=> array(),
        ));

        $translator = $this->getObject('translator');
        $attribs    = $this->buildAttributes($config->attribs);

        $html = array();

        $html[] = '<fieldset  name="'. $config->name .'" '. $attribs .'>';

        if(isset($config->legend)) {
            $html[] = '<legend>'.$config->translate ? $translator->translate( $config->legend ) : $config->legend.'</legend>';
        }

        foreach($config->options as $option)
        {
            $value = $option->value;
            $label = $config->translate ? $translator->translate( $option->label ) : $option->label;

            $extra = '';

            if ($config->selected instanceof KObjectConfig)
            {
                foreach ($config->selected as $selected)
                {
                    $selected = is_object( $selected ) ? $selected->{$config->value} : $selected;
                    if ($value == $selected)
                    {
                        $extra .= 'checked="checked"';
                        break;
                    }
                }
            }
            else $extra .= ($value == $config->selected) ? 'checked="checked"' : '';

            if(isset($option->disabled) && $option->disabled) {
                $extra .= 'disabled="disabled"';
            }

            if(isset($option->attribs)) {
                $attribs = $this->buildAttributes($option->attribs);
            }

            $html[] = '<label class="checkbox" for="'.$option->name.$option->id.'">';
            $html[] = '<input type="checkbox" name="'.$option->name.'[]" id="'.$option->name.$option->id.'" value="'.$value.'" '.$extra.' '.$attribs.' />';
            $html[] = $label;
            $html[] = '</label>';
        }

        $html[] = '</fieldset>';

        return implode(PHP_EOL, $html);
    }

	/**
	 * Generates an HTML boolean radio list
	 *
	 * @param   array|KObjectConfig     $config An optional array with configuration options
	 * @return  string  Html
	 */
    public function booleanlist($config = array())
    {
        $translator = $this->getObject('translator');

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name'   	=> '',
            'attribs'	=> array(),
            'true'		=> $translator->translate('Yes'),
            'false'		=> $translator->translate('No'),
            'selected'	=> null,
            'translate'	=> true
        ));

        $name    = $config->name;
        $attribs = $this->buildAttributes($config->attribs);

        $html  = array();

        $extra = $config->selected ? 'checked="checked"' : '';
        $text  = $config->translate ? $translator->translate( $config->true ) : $config->true;

        $html[] = '<label for="'.$name.'1" class="btn">';
        $html[] = '<input type="radio" name="'.$name.'" id="'.$name.'1" value="1" '.$extra.' '.$attribs.' />';
        $html[] = $text.'</label>';

        $extra = !$config->selected ? 'checked="checked"' : '';
        $text  = $config->translate ? $translator->translate( $config->false ) : $config->false;

        $html[] = '<label for="'.$name.'0" class="btn">';
        $html[] = '<input type="radio" name="'.$name.'" id="'.$name.'0" value="0" '.$extra.' '.$attribs.' />';
        $html[] = $text.'</label>';

        return implode(PHP_EOL, $html);
    }
}
