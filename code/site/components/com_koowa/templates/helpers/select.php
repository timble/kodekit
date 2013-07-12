<?php
/**
 * @version     $Id$
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Select Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperSelect extends KTemplateHelperSelect
{
	/**
	 * Generates an HTML boolean radio list
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
    public function booleanlist($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'name'   	=> '',
            'attribs'	=> array(),
            'true'		=> 'Yes',
            'false'		=> 'No',
            'selected'	=> null,
            'translate'	=> true,
            'wrap'		=> false
        ));

        $name    = $config->name;
        $attribs = KHelperArray::toString($config->attribs);

        $html  = array();

        if ($config->wrap) {
            $html[] = '<div class="controls">';
        }

        $extra = $config->selected ? 'checked="checked"' : '';
        $text  = $config->translate ? $this->translate( $config->true ) : $config->true;

        $html[] = '<label for="'.$name.'1" class="radio inline">';
        $html[] = '<input type="radio" class="radio inline" name="'.$name.'" id="'.$name.'1" value="1" '.$extra.' '.$attribs.' />';
        $html[] = $text.'</label>';

        $extra = !$config->selected ? 'checked="checked"' : '';
        $text  = $config->translate ? $this->translate( $config->false ) : $config->false;

        $html[] = '<label for="'.$name.'0" class="radio inline">';
        $html[] = '<input type="radio" class="radio inline" name="'.$name.'" id="'.$name.'0" value="0" '.$extra.' '.$attribs.' />';
        $html[] = $text.'</label>';

        if ($config->wrap) {
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
    
    /**
     * Generates an HTML check list
     *
     * @param 	array 	An optional array with configuration options
     * @return	string	Html
     */    
    public function checklist( $config = array())
    {
        $config = new KConfig($config);
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
        $attribs = KHelperArray::toString($config->attribs);

        $html = array();
        foreach ($config->list as $row) {
            $key  = $row->{$config->key};
            $text = $config->translate ? $this->translate( $row->{$config->text} ) : $row->{$config->text};
            $id	  = isset($row->id) ? $row->id : null;

            $extra = '';

            if ($config->selected instanceof KConfig) {
                foreach ($config->selected as $value) {
                    $sel = is_object( $value ) ? $value->{$config->key} : $value;
                    if ($key == $sel) {
                        $extra .= 'checked="checked"';
                        break;
                    }
                }
            } else $extra .= ($key == $config->selected) ? 'checked="checked"' : '';

            $html[] = '<label class="checkbox" for="'.$name.$key.'">';
            $html[] = '<input type="checkbox" name="'.$name.'[]" id="'.$name.$key.'" value="'.$key.'" '.$extra.' '.$attribs.' />';
            $html[] = $text;
            $html[] = '</label>';
        }

        return implode(PHP_EOL, $html);
    }
}