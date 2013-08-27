<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Behavior Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperBehavior extends KTemplateHelperAbstract
{
    /**
     * Array which holds a list of loaded Javascript libraries
     *
     * @type array
     */
    protected static $_loaded = array();

    /**
     * Loads Mootools from Joomla sources
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function koowa($config = array())
    {
        $html = '';

        if (!isset(self::$_loaded['koowa']))
        {
            $html .= $this->mootools();
            $html .= '<script src="media://koowa/com_koowa/js/koowa.js" />';

            self::$_loaded['koowa'] = true;
        }

        return $html;
    }

    /**
     * Loads jQuery
     *
     * Loads it from Joomla in 3.0+ and our own sources in 2.5. If debug config property is set, an uncompressed
     * version will be included.
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function jquery($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        $html = '';

        if (!isset(self::$_loaded['jquery']))
        {
            if (version_compare(JVERSION, '3.0', 'ge')) {
                JHtml::_('jquery.framework');
            } else {
                $html .= '<script src="media://koowa/com_koowa/js/jquery'.($config->debug ? '' : '.min').'.js" />';
            }

            self::$_loaded['jquery'] = true;
        }

        return $html;
    }

    /**
     * Loads Mootools from Joomla sources
     *
     * @param array|KObjectConfig $config
     * @return string
     */
	public function mootools($config = array())
	{
		if (!isset(self::$_loaded['mootools']))
		{
            if (version_compare(JVERSION, '3.0', 'ge')) {
                JHTML::_('behavior.framework', true);
            } else {
                JHTML::_('behavior.mootools', false);
            }

			self::$_loaded['mootools'] = true;
		}

		return '';
	}


    /**
     * Keeps session alive
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function keepalive($config = array())
    {
        JHtml::_('behavior.keepalive');
        return '';
    }

   	/**
	 * Render a modal box
     *
     * @param array|KObjectConfig $config
	 * @return string	The html output
	 */
	public function modal($config = array())
	{
		$config = new KObjectConfig($config);
		$config->append(array(
			'selector' => 'a.modal',
			'options'  => array('disableFx' => true)
 		));

        JHTML::_('behavior.modal', $config->selector, $config->toArray());
		return '';
	}


    /**
     * Render a tooltip
     *
     * @param array|KObjectConfig $config
     * @return string	The html output
     */
    public function tooltip($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'selector' => '.hasTip',
            'options'  => array()
        ));

        JHTML::_('behavior.tooltip', $config->selector, $config->toArray());

        return '';
    }

    /**
     * Loads the calendar behavior and attaches it to a specified element
     *
     * @param array|KObjectConfig $config
     * @return string	The html output
     */
    public function calendar($config = array())
    {
        static $loaded;

        if ($loaded === null)
        {
            $loaded = array();
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'value'   => '',

            'date'	  => gmdate("M d Y H:i:s"),
            'name'    => '',
            'format'  => '%Y-%m-%d %H:%M:%S',
            'attribs' => array('size' => 25, 'maxlength' => 19)
        ))->append(array(
                'id'      => 'button-'.$config->name,
            ));

        $html = '';

        if($config->date && $config->date != '0000-00-00 00:00:00' && $config->date != '0000-00-00') {
            $config->date = strftime($config->format, strtotime($config->date) /*+ $config->gmt_offset*/);
        }
        else $config->date = '';

        if (!isset(self::$_loaded['calendar']))
        {
            $html .= '<script src="media://com_koowa/js/datepicker.js" />';
            $html .= '<style src="media://com_koowa/css/datepicker.css" />';

            self::$_loaded['calendar'] = true;
        }

        $attribs = JArrayHelper::toString(KConfig::unbox($config->attribs));

        if ($config->attribs->readonly !== 'readonly' && $config->attribs->disabled !== 'disabled') {
            // Only display the triggers once for each control.
            if (!in_array($config->id, $loaded)) {
                $html .= "<script>
                    jQuery(function($){
                        $('#".$config->id."').datepicker({todayHighlight: true, parentEl: $('#".$config->id."').parent()});
                        /*
                        Calendar.setup({
                            inputField     :    '".$config->id."',
                            ifFormat       :    '".$config->format."',
                            button         :    '".$config->id."_img',
                            align          :    'Tl',
                            singleClick    :    true,
                            firstDay: '" . JFactory::getLanguage()->getFirstDay() . "'
                        });*/
                    });
                </script>";
                $loaded[] = $config->id;
            }

            $html .= '<div class="input-append date" data-date-format="'.$config->format.'" id="'.$config->id.'">';
            $html .= '<input type="text" name="'.$config->name.'" value="'.$config->value.'"  '.$attribs.' />';
            $html .= '<span class="add-on" >';
            $html .= '<i class="icon-calendar"></i>&zwnj;'; //&zwnj; is a zero width non-joiner, helps the button get the right height without adding to the width (like with &nbsp;)
            $html .= '</span>';
            $html .= '</div>';
        }
        else
        {
            $html = '';
            $html .= '<div class="input-append">';
            $html .= '<input type="text" name="'.$config->name.'" id="'.$config->id.'" value="'.$config->value.'" '.$attribs.' />';
            $html .= '</div>';
        }


        return $html;
    }


    /**
     * Renders an overlay
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function overlay($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'url'  		=> '',
            'options'  	=> array(),
            'attribs'	=> array(),
        ));

        $html = '';
        // Load the necessary files if they haven't yet been loaded
        if (!isset(self::$_loaded['overlay']))
        {
            $html .= $this->koowa();

            $html .= '
            <style>
            .-koowa-overlay-status {
                float: right;
                background-color:#FFFFDD;
                padding: 5px;
            }
            ';

            self::$_loaded['overlay'] = true;
        }

        $url = $this->getObject('koowa:http.url', array('url' => $config->url));
        if(!isset($url->query['format'])) {
            $url->query['format'] = 'overlay';
        }

        $attribs = $this->buildAttributes($config->attribs);

        $id = 'overlay'.rand();
        if($url->fragment)
        {
            //Allows multiple identical ids, legacy should be considered replaced with #$url->fragment instead
            $config->append(array(
                'options' => array(
                    'selector' => '[id='.$url->fragment.']'
                )
            ));
        }

        //Don't pass an empty array as options
        $options = $config->options->toArray() ? ', '.$config->options : '';
        $html .= "<script>window.addEvent('domready', function(){new Koowa.Overlay('$id'".$options.");});</script>";

        $html .= '<div data-url="'.$url.'" class="-koowa-overlay" id="'.$id.'" '.$attribs.'><div class="-koowa-overlay-status">'.$this->translate('Loading...').'</div></div>';
        return $html;
    }

    /**
     * Loads the Forms.Validator class and connects it to Koowa.Controller
     *
     * This allows you to do easy, CSS class based forms validation. Koowa.Controller.Form automatically works with it.
     *
     * @see    http://www.mootools.net/docs/more125/more/Forms/Form.Validator
     *
     * @param array|KObjectConfig $config
     * @return string	The html output
     */
    public function validator($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'selector' => '.-koowa-form',
            'options'  => array(
                'scrollToErrorsOnChange' => false,
                'scrollToErrorsOnBlur'   => false
            )
        ));

        $html = '';
        // Load the necessary files if they haven't yet been loaded
        if(!isset(self::$_loaded['validator']))
        {
            $html .= $this->mootools();
            $html .= $this->koowa();

            $html .= '<script src="media://koowa/com_koowa/js/validator.js" />';
            $html .= '<script src="media://koowa/com_koowa/js/patch.validator.js" />';

            self::$_loaded['validator'] = true;
        }

        //Don't pass an empty array as options
        $options = $config->options->toArray() ? ', '.$config->options : '';
        $html .= "<script>
		window.addEvent('domready', function(){
		    $$('$config->selector').each(function(form){
		        new Koowa.Validator(form".$options.");
		        form.addEvent('validate', form.validate.bind(form));
		    });
		});
		</script>";

        return $html;
    }

    /**
     * Loads the select2 behavior and attaches it to a specified element
     *
     * @see    http://ivaynberg.github.io/select2/select-2.1.html
     *
     * @param  array|KObjectConfig $config
     * @return string	The html output
     */
    public function select2($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug'),
            'element' => '.select2-listbox',
            'options' => array(
                'width' => 'resolve',
                'dropdownCssClass' => 'koowa'
            )
        ));

        $html = '';

        if (!isset(self::$_loaded['select2']))
        {
            $html .= $this->jquery();
            $html .= '<script src="media://koowa/com_koowa/js/select2'.($config->debug ? '' : '.min').'.js" />';
            $html .= '<style src="media://koowa/com_koowa/css/select2.css" />';

            self::$_loaded['select2'] = true;
        }

        if ($config->element)
        {
            $html .= '<script>jQuery(function($){
                $("'.$config->element.'").select2('.$config->options.');
                $("'.$config->element.'").select2(\'container\').removeClass(\'required\');
            });</script>';
        }

        return $html;
    }

    /**
     * Loads the autocomplete behavior and attaches it to a specified element
     *
     * @param  array|KObjectConfig $config
     * @return string	The html output
     */
    public function autocomplete($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'element'  => null,
            'options'  => array(
                'dropdownCssClass' => 'koowa',
                'validate'      => false, //Toggle if the forms validation helper is loaded
                'queryVarName'  => 'search',
                'width'         => 'resolve',
                'model'		    => $config->model,
                'placeholder'   => $config->prompt,
                'allowClear'    => $config->deselect,
                'value'         => $config->value,
                'text'          => $config->text,
                'selected'      => $config->selected,
                'url'           => $config->url
            )
        ));

        $html ='';

        // Load the necessary files if they haven't yet been loaded
        if(!isset(self::$_loaded['autocomplete']))
        {
            $html .= $this->select2(array('element' => false));
            $html .= '<script src="media://koowa/com_koowa/js/koowa.select2.js" />';
        }

        $html .= '<script>jQuery(function($){
                $("'.$config->element.'").koowaSelect2('.$config->options.');
                $("'.$config->element.'").koowaSelect2(\'container\').removeClass(\'required\');
            });</script>';

        return $html;
    }
}
