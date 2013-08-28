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
            'format'  => '%Y-%m-%d %H:%M:%S', //Passed to the js plugin as a data attribute
            'attribs' => array('size' => 25, 'maxlength' => 19, 'placeholder' => '') //@TODO placeholder fix for chrome may not be needed anymore
        ))->append(array(
             'id'      => 'button-'.$config->name,
             'options' => array(
                 'todayBtn' => true,
                 'todayHighlight' => true,
                 'language' => JFactory::getLanguage()->getTag(),
                 'autoclose' => true //Same as singleClick in previous js plugin
             )
            ));

        // Handle the special case for "now".
        if (strtoupper($config->value) == 'NOW')
        {
            $config->value = strftime($config->format);
        }

        $html = '';


        if($config->date && $config->date != '0000-00-00 00:00:00' && $config->date != '0000-00-00') {
            $config->date = strftime($config->format, strtotime($config->date) /*+ $config->gmt_offset*/);
        }
        else $config->date = '';

        // @TODO this is legacy, or bc support, and may not be compitable with strftime and the like
        $config->format = str_replace(
            array('%Y', '%y', '%m', '%d', '%H', '%M', '%S'),
            array('yyyy', 'yy', 'mm', 'dd', 'h', 'i', 's'),
            $config->format
        );

        switch (strtoupper($config->filter))
        {
            case 'SERVER_UTC':
                // Convert a date to UTC based on the server timezone.
                if (intval($config->value))
                {
                    // Get a date object based on the correct timezone.
                    $date = JFactory::getDate($config->value, 'UTC');
                    $date->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));

                    // Transform the date string.
                    $config->value = $date->format('Y-m-d H:i:s', true, false);
                }
                break;

            case 'USER_UTC':
                // Convert a date to UTC based on the user timezone.
                if (intval($config->value))
                {
                    // Get a date object based on the correct timezone.
                    $date = JFactory::getDate($config->value, 'UTC');
                    $date->setTimezone(new DateTimeZone(JFactory::getUser()->getParam('timezone', JFactory::getConfig()->get('offset'))));

                    // Transform the date string.
                    $config->value = $date->format('Y-m-d H:i:s', true, false);
                }
                break;
        }

        if (!isset(self::$_loaded['calendar']))
        {
            $html .= '<script src="media://koowa/com_koowa/js/datepicker.js" />';
            $html .= '<style src="media://koowa/com_koowa/css/datepicker.css" />';

            $locale = array(
                'days'  =>  array(
                    $this->translate('Sunday'),
                    $this->translate('Monday'),
                    $this->translate('Tuesday'),
                    $this->translate('Wednesday'),
                    $this->translate('Thursday'),
                    $this->translate('Friday'),
                    $this->translate('Saturday'),
                    $this->translate('Sunday')
                ),
                'daysShort' => array(
                    $this->translate('Sun'),
                    $this->translate('Mon'),
                    $this->translate('Tue'),
                    $this->translate('Wed'),
                    $this->translate('Thu'),
                    $this->translate('Fri'),
                    $this->translate('Sat'),
                    $this->translate('Sun')
                ),
                'daysMin' => array(
                    $this->translate('Su'),
                    $this->translate('Mo'),
                    $this->translate('Tu'),
                    $this->translate('We'),
                    $this->translate('Th'),
                    $this->translate('Fr'),
                    $this->translate('Sa'),
                    $this->translate('Su')
                ),
                'months' => array(
                    $this->translate('January'),
                    $this->translate('February'),
                    $this->translate('March'),
                    $this->translate('April'),
                    $this->translate('May'),
                    $this->translate('June'),
                    $this->translate('July'),
                    $this->translate('August'),
                    $this->translate('September'),
                    $this->translate('October'),
                    $this->translate('November'),
                    $this->translate('December')
                ),
                'monthsShort' => array(
                    $this->translate('January_short'),
                    $this->translate('February_short'),
                    $this->translate('March_short'),
                    $this->translate('April_short'),
                    $this->translate('May_short'),
                    $this->translate('June_short'),
                    $this->translate('July_short'),
                    $this->translate('August_short'),
                    $this->translate('September_short'),
                    $this->translate('October_short'),
                    $this->translate('November_short'),
                    $this->translate('December_short')
                ),
                'today' => $this->translate('Today')
            );


            $locale['weekStart'] = JFactory::getLanguage()->getFirstDay();
            // Required locale
            $html .= '<script>
            (function($){
                $.fn.datepicker.dates['.json_encode($config->options->language).'] = '.json_encode($locale).';
            }(jQuery));
            </script>';

            self::$_loaded['calendar'] = true;
        }

        $attribs = $this->buildAttributes($config->attribs);

        if ($config->attribs->readonly !== 'readonly' && $config->attribs->disabled !== 'disabled') {
            // Only display the triggers once for each control.
            if (!in_array($config->id, $loaded)) {
                $html .= "<script>
                    jQuery(function($){
                        var options = ".$config->options.";
                        if(!options.hasOwnProperty('parentEl')) {
                            options.parentEl = $('#".$config->id."').parent();
                        }
                        $('#".$config->id."').datepicker(options);
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
