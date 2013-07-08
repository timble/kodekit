<?php
/**
 * @version		$Id$
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Behavior Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperBehavior extends KTemplateHelperAbstract
{
	/**
	 * Array which holds a list of loaded javascript libraries
	 *
	 * boolean
	 */
	protected static $_loaded = array();

	/**
	 * Method to load the mootools framework into the document head
	 *
	 * - If debugging mode is on an uncompressed version of mootools is included for easier debugging.
	 *
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 */
	public function mootools($config = array())
	{
		$config = new KConfig($config);
		$html ='';

		// Only load once
		if (!isset(self::$_loaded['mootools'])) 
		{
			$html .= '<script src="media://lib_koowa/js/mootools.js" />';
			self::$_loaded['mootools'] = true;
		}

		return $html;
	}

	/**
	 * Render a modal box
	 *
	 * @return string	The html output
	 */
	public function modal($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'selector' => 'a.modal',
			'options'  => array('disableFx' => true)
 		));

 		$html = '';

		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['modal']))
		{
			$html .= '<script src="media://lib_koowa/js/modal.js" />';
			$html .= '<style src="media://lib_koowa/css/modal.css" />';

			self::$_loaded['modal'] = true;
		}

		$signature = md5(serialize(array($config->selector,$config->options)));
		if (!isset(self::$_loaded[$signature]))
		{
			$options = !empty($config->options) ? $config->options->toArray() : array();
			$html .= "
			<script>
				window.addEvent('domready', function() {

				SqueezeBox.initialize(".json_encode($options).");
				SqueezeBox.assign($$('".$config->selector."'), {
			        parse: 'rel'
				});
			});
			</script>";

			self::$_loaded[$signature] = true;
		}

		return $html;
	}

	/**
	 * Render a tooltip
	 *
	 * @return string	The html output
	 */
	public function tooltip($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'selector' => '.hasTip',
			'options'  => array()
 		));

 		$html = '';

		$signature = md5(serialize(array($config->selector,$config->options)));
		if (!isset(self::$_loaded[$signature]))
		{
		    //Don't pass an empty array as options
			$options = $config->options->toArray() ? ', '.$config->options : '';
			$html .= "
			<script>
				window.addEvent('domready', function(){ new Tips($$('".$config->selector."')".$options."); });
			</script>";

			self::$_loaded[$signature] = true;
		}

		return $html;
	}

	/**
	 * Render an overlay
	 *
	 * @return string	The html output
	 */
	public function overlay($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'url'  		=> '',
			'options'  	=> array(),
			'attribs'	=> array(),
		));

		$html = '';
		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['overlay']))
		{
			$html .= '<script src="media://lib_koowa/js/koowa.js" />';
			$html .= '<style src="media://lib_koowa/css/koowa.css" />';

			self::$_loaded['overlay'] = true;
		}

		$url = $this->getService('koowa:http.url', array('url' => $config->url));
		if(!isset($url->query['tmpl'])) {
		    $url->query['tmpl'] = '';
		}

		$attribs = KHelperArray::toString($config->attribs);

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
	 * Keep session alive
	 *
	 * This will send an ascynchronous request to the server via AJAX on an interval
	 * in miliseconds
	 *
	 * @return string	The html output
	 */
	public function keepalive($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'refresh'  => 15 * 60000, //15min
		    'url'	   => KRequest::url()
		));

		$refresh = (int) $config->refresh;

	    // Longest refresh period is one hour to prevent integer overflow.
		if ($refresh > 3600000 || $refresh <= 0) {
			$refresh = 3600000;
		}

		// Build the keepalive script.
		$html =
		"<script>
			Koowa.keepalive =  function() {
				var request = new Request({method: 'get', url: '".$config->url."'}).send();
			}

			window.addEvent('domready', function() { Koowa.keepalive.periodical('".$refresh."'); });
		</script>";

		return $html;
	}
	
	/**
	 * Loads the Forms.Validator class and connects it to Koowa.Controller
	 *
	 * This allows you to do easy, css class based forms validation-
	 * Koowa.Controller.Form works with it automatically.
	 * Requires koowa.js and mootools to be loaded in order to work.
	 *
	 * @see    http://www.mootools.net/docs/more125/more/Forms/Form.Validator
	 *
	 * @return string	The html output
	 */
	public function validator($config = array())
	{
	    $config = new KConfig($config);
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
		    if(version_compare(JVERSION,'1.6.0','ge')) {
		        $html .= '<script src="media://lib_koowa/js/validator-1.3.js" />';
		    } else {
		        $html .= '<script src="media://lib_koowa/js/validator-1.2.js" />';
		    }
		    $html .= '<script src="media://lib_koowa/js/patch.validator.js" />';

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
	 * Loads the autocomplete behavior and attaches it to a specified element
	 *
	 * @see    http://mootools.net/forge/p/meio_autocomplete
	 * @return string	The html output
	 */
	public function autocomplete($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'identifier'    => null,
			'element'       => null,
			'path'          => 'name',
			'filter'		=> array(),
		    'validate'		=> true,
		    'selected'		=> null	
		))->append(array(
		    'value_element' => $config->element.'-value',
		    'attribs' => array(
		        'id'    => $config->element,
		        'type'  => 'text',
		        'class' => 'inputbox value',
		        'size'	=> 60
		    ),
		))->append(array(
			'options' => array( 
		        'valueField'     => $config->value_element,
		        'filter'         => array('path' => $config->path),
				'requestOptions' => array('method' => 'get'),
		        'urlOptions'	 => array(
		    		'queryVarName' => 'search',
		        	'extraParams'  => KConfig::unbox($config->filter)
		        )
		    )
		));

		if($config->validate) 
		{
		    $config->attribs['data-value']  = $config->value_element;
		    $config->attribs['data-value'] .= ' ma-required';
		}
		
		if(!isset($config->url)) 
		{
		    $identifier = $this->getIdentifier($config->identifier);
		    $config->url = JRoute::_('index.php?option=com_'.$identifier->package.'&view='.$identifier->name.'&format=json', false);
		}
		    
		$html = '';
		
		// Load the necessary files if they haven't yet been loaded
		if(!isset(self::$_loaded['autocomplete']))
		{
            if(version_compare(JVERSION, '3.0', 'ge')) {
                $html .= '<script src="media://lib_koowa/js/autocomplete-2.0.js" />';
            } else {
                $html .= '<script src="media://lib_koowa/js/autocomplete-1.0.js" />';
            }
		    $html .= '<script src="media://lib_koowa/js/patch.autocomplete.js" />';
		    $html .= '<style src="media://lib_koowa/css/autocomplete.css" />';
		}

		$html .= "
		<script>
			window.addEvent('domready', function(){				
				new Koowa.Autocomplete(document.id('".$config->element."'), ".json_encode($config->url).", ".json_encode(KConfig::unbox($config->options)).");
			});
		</script>";
		
		$html .= '<input '.KHelperArray::toString($config->attribs).' />';
	    $html .= '<input '.KHelperArray::toString(array(
            'type'  => 'hidden',
            'name'  => $config->name,
            'id'    => $config->element.'-value',
            'value' => $config->selected
	       )).' />';

	    return $html;
	}
	
	/**
	 * Loads the calendar behavior and attaches it to a specified element
	 *
	 * @return string	The html output
	 */
    public function calendar($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'date'	  => gmdate("M d Y H:i:s"),
		    'name'    => '',
		    'format'  => '%Y-%m-%d %H:%M:%S',
		    'attribs' => array('size' => 25, 'maxlength' => 19)
 		));
 
        if($config->date && $config->date != '0000-00-00 00:00:00' && $config->date != '0000-00-00') { 
            $config->date = strftime($config->format, strtotime($config->date) /*+ $config->gmt_offset*/);
        }
        else $config->date = '';
        
	    $html = '';
		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['calendar']))
		{
			$html .= '<script src="media://lib_koowa/js/calendar.js" />';
			$html .= '<script src="media://lib_koowa/js/calendar-setup.js" />';
			$html .= '<style src="media://lib_koowa/css/calendar.css" />';
			
			$html .= '<script>'.$this->_calendarTranslation().'</script>';

			self::$_loaded['calendar'] = true;
		}
	   
		$html .= "<script>
					window.addEvent('domready', function() {Calendar.setup({
        				inputField     :    '".$config->name."',     	 
        				ifFormat       :    '".$config->format."',   
        				button         :    'button-".$config->name."', 
        				align          :    'Tl',
        				singleClick    :    true,
        				showsTime	   :    false
    				});});
    			</script>";
		
		$attribs = KHelperArray::toString($config->attribs);

   		$html .= '<input type="text" name="'.$config->name.'" id="'.$config->name.'" value="'.$config->date.'" '.$attribs.' />';
		$html .= '<img class="calendar" src="media://lib_koowa/images/calendar.png" alt="calendar" id="button-'.$config->name.'" />';
		
		return $html;
	}
	
	/**
	 * Method to get the internationalisation script/settings for the JavaScript Calendar behavior.
	 *
	 * @return string	The html output
	 */
	protected function _calendarTranslation()
	{
		// Build the day names array.
		$dayNames = array(
			'"'.$this->translate('Sunday').'"',
			'"'.$this->translate('Monday').'"',
			'"'.$this->translate('Tuesday').'"',
			'"'.$this->translate('Wednesday').'"',
			'"'.$this->translate('Thursday').'"',
			'"'.$this->translate('Friday').'"',
			'"'.$this->translate('Saturday').'"',
			'"'.$this->translate('Sunday').'"'
		);

		// Build the short day names array.
		$shortDayNames = array(
			'"'.$this->translate('Sun').'"',
			'"'.$this->translate('Mon').'"',
			'"'.$this->translate('Tue').'"',
			'"'.$this->translate('Wed').'"',
			'"'.$this->translate('Thu').'"',
			'"'.$this->translate('Fri').'"',
			'"'.$this->translate('Sat').'"',
			'"'.$this->translate('Sun').'"'
		);

		// Build the month names array.
		$monthNames = array(
			'"'.$this->translate('January').'"',
			'"'.$this->translate('February').'"',
			'"'.$this->translate('March').'"',
			'"'.$this->translate('April').'"',
			'"'.$this->translate('May').'"',
			'"'.$this->translate('June').'"',
			'"'.$this->translate('July').'"',
			'"'.$this->translate('August').'"',
			'"'.$this->translate('September').'"',
			'"'.$this->translate('October').'"',
			'"'.$this->translate('November').'"',
			'"'.$this->translate('December').'"'
		);

		// Build the short month names array.
		$shortMonthNames = array(
			'"'.$this->translate('January_short').'"',
			'"'.$this->translate('February_short').'"',
			'"'.$this->translate('March_short').'"',
			'"'.$this->translate('April_short').'"',
			'"'.$this->translate('May_short').'"',
			'"'.$this->translate('June_short').'"',
			'"'.$this->translate('July_short').'"',
			'"'.$this->translate('August_short').'"',
			'"'.$this->translate('September_short').'"',
			'"'.$this->translate('October_short').'"',
			'"'.$this->translate('November_short').'"',
			'"'.$this->translate('December_short').'"'
		);

		// Build the script.
		$i18n = array(
			'// Calendar i18n Setup.',
			'Calendar._FD = 0;',
			'Calendar._DN = new Array ('.implode(', ', $dayNames).');',
			'Calendar._SDN = new Array ('.implode(', ', $shortDayNames).');',
			'Calendar._MN = new Array ('.implode(', ', $monthNames).');',
			'Calendar._SMN = new Array ('.implode(', ', $shortMonthNames).');',
			'',
			'Calendar._TT = {};',
			'Calendar._TT["INFO"] = "'.$this->translate('About the calendar').'";',
			'Calendar._TT["PREV_YEAR"] = "'.$this->translate('Prev. year (hold for menu)').'";',
			'Calendar._TT["PREV_MONTH"] = "'.$this->translate('Prev. month (hold for menu)').'";',
			'Calendar._TT["GO_TODAY"] = "'.$this->translate('Go Today').'";',
			'Calendar._TT["NEXT_MONTH"] = "'.$this->translate('Next month (hold for menu)').'";',
			'Calendar._TT["NEXT_YEAR"] = "'.$this->translate('Next year (hold for menu)').'";',
			'Calendar._TT["SEL_DATE"] = "'.$this->translate('Select date').'";',
			'Calendar._TT["DRAG_TO_MOVE"] = "'.$this->translate('Drag to move').'";',
			'Calendar._TT["PART_TODAY"] = "('.$this->translate('Today').')";',
			'Calendar._TT["DAY_FIRST"] = "'.$this->translate('Display %s first').'";',
			'Calendar._TT["WEEKEND"] = "0,6";',
			'Calendar._TT["CLOSE"] = "'.$this->translate('Close').'";',
			'Calendar._TT["TODAY"] = "'.$this->translate('Today').'";',
			'Calendar._TT["TIME_PART"] = "'.$this->translate('(Shift-)Click or drag to change value').'";',
			'Calendar._TT["DEF_DATE_FORMAT"] = "'.$this->translate('%Y-%m-%d').'";',
			'Calendar._TT["TT_DATE_FORMAT"] = "'.$this->translate('%a, %b %e').'";',
			'Calendar._TT["WK"] = "'.$this->translate('wk').'";',
			'Calendar._TT["TIME"] = "'.$this->translate('Time:').'";',
			'',
			'"Date selection:\n" +',
			'"- Use the \xab, \xbb buttons to select year\n" +',
			'"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +',
			'"- Hold mouse button on any of the above buttons for faster selection.";',
			'',
			'Calendar._TT["ABOUT_TIME"] = "\n\n" +',
			'"Time selection:\n" +',
			'"- Click on any of the time parts to increase it\n" +',
			'"- or Shift-click to decrease it\n" +',
			'"- or click and drag for faster selection.";',
			''
		);

		return implode("\n", $i18n);
	}
}