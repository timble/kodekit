<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Select Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperSelect extends KTemplateHelperAbstract
{
    /**
     * Generates an HTML select option
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function option($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'value' 	=> null,
            'text'   	=> '',
            'disable'	=> false,
            'attribs'	=> array(),
        ));

        $option = new stdClass;
        $option->value 	  = $config->value;
        $option->text  	  = trim($config->text) ? $config->text : $config->value;
        $option->disable  = $config->disable;
        $option->attribs  = $config->attribs;

        return $option;
    }

    /**
     * Enhances a select box using Select2
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    protected function _optionlistSelect2($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'prompt'  => '- Select -',
            'attribs' => array()
        ))->append(array(
            'select2_options' => array(
                'element' => $config->attribs->id ? '#'.$config->attribs->id : 'select[name='.$config->name.']',
                'options' => array()
            )
        ));

        $html = '';

        if ($config->deselect)
        {
            $config->select2_options->append(array('options' => array(
                'placeholder' => $this->translate($config->prompt),
                'allowClear'  => true
            )));
        }

        $html .= $this->getTemplate()->getHelper('behavior')->select2($config->select2_options);

        return $html;
    }

    /**
     * Generates an HTML select list
     *
     * @param 	array|KObjectConfig 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function optionlist($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'options' 	=> array(),
            'name'   	=> 'id',
            'attribs'	=> array('size' => 1),
            'selected'	=> null,
            'translate'	=> false,
            'select2'   => false
        ));

        $name    = $config->name;
        $attribs = $this->buildAttributes($config->attribs);

        $html = array();

        if ($config->select2) {
            if ($config->deselect && !$config->attribs->multiple)
            {
                // select2 needs the first option empty for placeholders to work on single select boxes
                $config->options = array_merge(array($this->option(array('text' => ''))),
                    $config->options->toArray());
            }

            $html[] = $this->_optionlistSelect2($config);
        }

        $html[] = '<select name="'. $name .'" '. $attribs .'>';

        foreach($config->options as $group => $options)
        {
            if (is_numeric($group)) {
                $options = array($options);
            } else {
                $html[] = '<optgroup label="' . $this->escape($group) . '">';
            }

            foreach ($options as $option)
            {
                $value  = $option->value;
                $text   = $config->translate ? $this->translate( $option->text ) : $option->text;

                $extra = '';
                if(isset($option->disable) && $option->disable) {
                    $extra .= 'disabled="disabled"';
                }

                if(isset($option->attribs)) {
                    $extra .= ' '.$this->buildAttributes($option->attribs);;
                }

                if(!is_null($config->selected))
                {
                    if ($config->selected instanceof KObjectConfig)
                    {
                        foreach ($config->selected as $selected)
                        {
                            $sel = is_object( $selected ) ? $selected->value : $selected;
                            if ((string) $value == (string) $sel)
                            {
                                $extra .= 'selected="selected"';
                                break;
                            }
                        }
                    }
                    else $extra .= ((string) $value == (string) $config->selected ? ' selected="selected"' : '');
                }

                $html[] = '<option value="'. $value .'" '. $extra .'>' . $text . '</option>';
            }

            if (!is_numeric($group)) {
                $html[] = '</optgroup>';
            }
        }

        $html[] = '</select>';

        return implode(PHP_EOL, $html);
    }

	/**
	 * Generates an HTML boolean radio list
	 *
	 * @param 	array|KObjectConfig 	$config An optional array with configuration options
	 * @return	string	Html
	 */
    public function booleanlist($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name'   	=> '',
            'attribs'	=> array(),
            'true'		=> $this->translate('Yes'),
            'false'		=> $this->translate('No'),
            'selected'	=> null,
            'translate'	=> true
        ));

        $name    = $config->name;
        $attribs = $this->buildAttributes($config->attribs);

        $html  = array();

        $extra = $config->selected ? 'checked="checked"' : '';
        $text  = $config->translate ? $this->translate( $config->true ) : $config->true;

        $html[] = '<label for="'.$name.'1" class="btn">';
        $html[] = '<input type="radio" name="'.$name.'" id="'.$name.'1" value="1" '.$extra.' '.$attribs.' />';
        $html[] = $text.'</label>';

        $extra = !$config->selected ? 'checked="checked"' : '';
        $text  = $config->translate ? $this->translate( $config->false ) : $config->false;

        $html[] = '<label for="'.$name.'0" class="btn">';
        $html[] = '<input type="radio" name="'.$name.'" id="'.$name.'0" value="0" '.$extra.' '.$attribs.' />';
        $html[] = $text.'</label>';

        return implode(PHP_EOL, $html);
    }
    
    /**
     * Generates an HTML check list
     *
     * @param 	array|KObjectConfig 	$config An optional array with configuration options
     * @return	string	Html
     */    
    public function checklist( $config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
                'list' 		=> null,
                'name'   	=> 'id',
                'attribs'	=> array(),
                'key'		=> 'id',
                'text'		=> 'title',
                'selected'	=> null,
                'translate'	=> false
        ));

        $name    = $config->name;
        $attribs = $this->buildAttributes($config->attribs);

        $html = array();
        foreach ($config->list as $row)
        {
            $key  = $row->{$config->key};
            $text = $config->translate ? $this->translate($row->{$config->text}) : $row->{$config->text};

            $extra = '';

            if ($config->selected instanceof KObjectConfig)
            {
                foreach ($config->selected as $value)
                {
                    $sel = is_object( $value ) ? $value->{$config->key} : $value;
                    if ($key == $sel) {
                        $extra .= 'checked="checked"';
                        break;
                    }
                }
            }
            else $extra .= ($key == $config->selected) ? 'checked="checked"' : '';

            $html[] = '<label class="checkbox" for="'.$name.$key.'">';
            $html[] = '<input type="checkbox" name="'.$name.'[]" id="'.$name.$key.'" value="'.$key.'" '.$extra.' '.$attribs.' />';
            $html[] = $text;
            $html[] = '</label>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Generates an HTML radio list
     *
     * @param 	array|KObjectConfig 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function radiolist($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'list' 		=> null,
            'name'   	=> 'id',
            'attribs'	=> array(),
            'key'		=> 'id',
            'text'		=> 'title',
            'selected'	=> null,
            'translate'	=> false
        ));

        $name    = $config->name;
        $attribs = $this->buildAttributes($config->attribs);

        $html = array();
        foreach($config->list as $row)
        {
            $key  = $row->{$config->key};
            $text = $config->translate ? $this->translate( $row->{$config->text} ) : $row->{$config->text};
            $id	  = isset($row->id) ? $row->id : null;

            $extra = '';

            if ($config->selected instanceof KObjectConfig)
            {
                foreach ($config->selected as $value)
                {
                    $sel = is_object( $value ) ? $value->{$config->key} : $value;
                    if ($key == $sel)
                    {
                        $extra .= 'selected="selected"';
                        break;
                    }
                }
            }
            else $extra .= ($key == $config->selected ? 'checked="checked"' : '');

            $html[] = '<input type="radio" name="'.$name.'" id="'.$name.$id.'" value="'.$key.'" '.$extra.' '.$attribs.' />';
            $html[] = '<label for="'.$name.$id.'">'.$text.'</label>';
            $html[] = '<br />';
        }

        return implode(PHP_EOL, $html);
    }
}
