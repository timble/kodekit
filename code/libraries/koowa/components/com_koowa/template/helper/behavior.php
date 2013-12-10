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
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        $html = '';

        if (!isset(self::$_loaded['koowa']))
        {
            $html .= $this->jquery();
            $html .= '<script src="media://koowa/com_koowa/js/koowa.js" />';

            self::$_loaded['koowa'] = true;
        }

        return $html;
    }

    /**
     * Loads icon font
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function icons($config = array())
    {
        $html = '';

        if (!isset(self::$_loaded['icons-css']))
        {
            $html .= '<style src="media://koowa/com_koowa/css/icons.css" />';
            self::$_loaded['icons-css'] = true;
        }

        return $html;
    }

    /**
     * Loads jQuery under a global variable called kQuery.
     *
     * Loads it from Joomla in 3.0+ and our own sources in 2.5. If debug config property is set, an uncompressed
     * version will be included.
     *
     * You can do window.jQuery = window.$ = window.kQuery; to use the default names
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function jquery($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        $html = '';

        if (!isset(self::$_loaded['jquery']))
        {
            if (version_compare(JVERSION, '3.0', 'ge'))
            {
                JHtml::_('jquery.framework');
                JHtml::_('script', 'media/koowa/com_koowa/js/koowa.jquery.js', false, false, false, false, false);

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
		$config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'    => JFactory::getApplication()->getCfg('debug'),
            'selector' => '.koowa-modal',
            'data'     => 'koowa-modal',
            'options'  => array('type' => 'image')
        ));

        $html = '';

        if(!isset(self::$_loaded['modal']))
        {
            $html .= $this->jquery();
            $html .= '<script src="media://koowa/com_koowa/js/jquery.magnific-popup'.($config->debug ? '' : '.min').'.js" />';

            self::$_loaded['modal'] = true;
        }

        if (!isset(self::$_loaded['modal-css']))
        {
            $html .= '<style src="media://koowa/com_koowa/css/modal.css" />';

            self::$_loaded['modal-css'] = true;
        }

        $options   = json_encode($config->options->toArray());
        $signature = md5('modal-'.$config->selector.$options);

        if(!isset(self::$_loaded[$signature]))
        {
            $html .= "<script>
            kQuery(function($){
                $('$config->selector').each(function(idx, el) {
                    var el = $(el);
                    var data = el.data('$config->data');
                    var options = $.parseJSON('$options');
                    if (data) {
                        $.extend(true, options, data);
                    }
                    el.magnificPopup(options);
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
     * @param array|KObjectConfig $config
     * @return string	The html output
     */
    public function tooltip($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.koowa-tooltip',
            'data'     => 'koowa-tooltip',
            'options'  => array()
        ));

        $html = '';

        if(!isset(self::$_loaded['tooltip']))
        {
            $html .= $this->jquery();

            // Load Boostrap with JS plugins.
            $identifier = clone $this->getIdentifier();
            $identifier->name = 'bootstrap';
            $html .= $this->getObject($identifier)->load(array('javascript' => true));

            self::$_loaded['tooltip'] = true;
        }

        $options = json_encode($config->options->toArray());

        $signature = md5('tooltip-'.$config->selector.$options);

        if(!isset(self::$_loaded[$signature]))
        {
            $html .= "<script>
                kQuery(function($) {
                    $('$config->selector').each(function(idx, el) {
                        var el = $(el);
                        var data = el.data('$config->data');
                        var options = $.parseJSON('$options');
                        if (data) {
                            $.extend(true, options, data);
                        }
                        el.tooltip(options);
                        });
                });
            </script>";

        }

        return $html;
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

        if ($loaded === null) {
            $loaded = array();
        }

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'value'	  => gmdate("M d Y H:i:s"),
            'name'    => '',
            'format'  => '%Y-%m-%d %H:%M:%S', //Passed to the js plugin as a data attribute
            'attribs' => array(
                'size' => 25,
                'maxlength' => 19,
                'placeholder' => '', //@TODO placeholder fix for chrome may not be needed anymore
                'oninput' => 'if(kQuery(this).data(\'datepicker\'))kQuery(this).data(\'datepicker\').update();'//@NOTE to allow editing timestamps
            )
        ))->append(array(
             'id'      => 'datepicker-'.$config->name,
             'options' => array(
                 'todayBtn' => 'linked',
                 'todayHighlight' => true,
                 'language' => JFactory::getLanguage()->getTag(),
                 'autoclose' => true, //Same as singleClick in previous js plugin,
                 'keyboardNavigation' => false, //To allow editing timestamps,
                 'calendarWeeks' => true, //Old datepicker used to display these
                 //'orientation' => 'auto right' //popover arrow set to point at the datepicker icon
             )
        ))->append(array(
            'options' => array(
                'parentEl' => '#'.$config->id
            )
        ));

        // Handle the special case for "now".
        if (strtoupper($config->value) == 'NOW') {
            $config->value = strftime($config->format);
        }

        $html = '';


        if($config->value && $config->value != '0000-00-00 00:00:00' && $config->value != '0000-00-00') {
            $config->value = strftime($config->format, strtotime($config->value) /*+ $config->gmt_offset*/);
        } else {
            $config->value = '';
        }

        // @TODO this is legacy, or bc support, and may not be compitable with strftime and the like
        $config->format = str_replace(
            array('%Y', '%y', '%m', '%d', '%H', '%M', '%S'),
            array('yyyy', 'yy', 'mm', 'dd', 'hh', 'ii', 'ss'),
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

            $locale = array(
                'days'  =>  array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
                'daysShort' => array('Sun','Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
                'daysMin' => array('Su','Mo','Tu','We','Th','Fr','Sa','Su'),
                'months' => array('January','February','March','April','May','June','July','August','September','October','November','December'),
                'monthsShort' => array('January_short','February_short','March_short','April_short','May_short','June_short','July_short','August_short','September_short','October_short','November_short','December_short')
            );
            foreach($locale as $key => $item){
                $locale[$key] = array_map(array($this, 'translate'), $item);
            }
            $locale['today']     = $this->translate('Today');
            $locale['clear']     = $this->translate('Clear');
            $locale['weekStart'] = JFactory::getLanguage()->getFirstDay();

            $html .= '<script>
            (function($){
                $.fn.datepicker.dates['.json_encode($config->options->language).'] = '.json_encode($locale).';
            }(kQuery));
            </script>';

            self::$_loaded['calendar'] = true;
        }

        if (!isset(self::$_loaded['calendar-css']))
        {
            $html .= '<style src="media://koowa/com_koowa/css/datepicker.css" />';

            self::$_loaded['calendar-css'] = true;
        }

        $attribs = $this->buildAttributes($config->attribs);

        if ($config->attribs->readonly !== 'readonly' && $config->attribs->disabled !== 'disabled')
        {
            // Only display the triggers once for each control.
            if (!in_array($config->id, $loaded))
            {
                $html .= "<script>
                    kQuery(function($){
                        var options = ".$config->options.";
                        if(!options.hasOwnProperty('parentEl')) {
                            options.parentEl = $('#".$config->id."').parent();
                        }
                        $('#".$config->id."').datepicker(options);
                    });
                </script>";
                $loaded[] = $config->id;
            }

            $html .= '<div class="input-append date datepicker" data-date-format="'.$config->format.'" id="'.$config->id.'">';
            $html .= '<input type="text" name="'.$config->name.'" value="'.$config->value.'"  '.$attribs.' />';
            $html .= '<span class="add-on btn" >';
            $html .= '<i class="icon-calendar icon-th"></i>&zwnj;'; //&zwnj; is a zero width non-joiner, helps the button get the right height without adding to the width (like with &nbsp;)
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
        $config = new KObjectConfigJson($config);
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
            $html .= '<script src="media://koowa/com_koowa/js/koowa.overlay.js" />';

            $html .= '
            <style>
            .-koowa-overlay-status {
                float: right;
                background-color:#FFFFDD;
                padding: 5px;
            }
            </style>
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

        $config->options->url = (string) $url;

        //Don't pass an empty array as options
        $options = json_encode($config->options->toArray());
        $html .= sprintf("<script>kQuery(function(){ new Koowa.Overlay('#%s', %s);});</script>", $id, $options);

        $html .= '<div class="-koowa-overlay" id="'.$id.'" '.$attribs.'><div class="-koowa-overlay-status">'.$this->translate('Loading...').'</div></div>';
        return $html;
    }

    /**
     * Loads the Forms.Validator class and connects it to Koowa.Controller.Form
     *
     * @param array|KObjectConfig $config
     * @return string	The html output
     */
    public function validator($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug'),
            'selector' => '.-koowa-form',
            'options'  => array(
                'ignoreTitle' => true,
                'onsubmit'    => false // We run the validation ourselves
            )
        ));

        $html = '';

        if(!isset(self::$_loaded['validator']))
        {
            $html .= $this->jquery();
            $html .= $this->koowa();

            $html .= '<script src="media://koowa/com_koowa/js/jquery.validate'.($config->debug ? '' : '.min').'.js" />';
            $html .= '<script src="media://koowa/com_koowa/js/patch.validator.js" />';

            self::$_loaded['validator'] = true;
        }

        $options   = json_encode($config->options->toArray());
        $signature = md5('validator-'.$config->selector.$options);

        if(!isset(self::$_loaded[$signature]))
        {
            $html .= "<script>
            kQuery(function($){
                $('$config->selector').on('koowa:validate', function(event){
                    if(!$(this).valid()) {
                        event.preventDefault();
                    }
                }).validate($options);
            });
            </script>";

            self::$_loaded[$signature] = true;
        }

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
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'cleanup' => false,
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
            $html .= '<script src="media://koowa/com_koowa/js/koowa.select2.js" />';

            self::$_loaded['select2'] = true;
        }

        if (!isset(self::$_loaded['select2-css']))
        {
            $html .= '<style src="media://koowa/com_koowa/css/select2.css" />';

            self::$_loaded['select2-css'] = true;
        }

        $options   = $config->options;
        $signature = md5('select2-'.$config->element.$options);

        if($config->element && !isset(self::$_loaded[$signature]))
        {
            $html .= '<script>
            kQuery(function($){
                $("'.$config->element.'").select2('.$options.');
                $("'.$config->element.'").select2(\'container\').removeClass(\'required\');
            });</script>';

            self::$_loaded[$signature] = true;
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
        $config = new KObjectConfigJson($config);
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
                'url'           => $config->url,
                'multiple'      => false
            )
        ));

        $html ='';

        $options   = $config->options;
        $signature = md5('autocomplete-'.$config->element.$options);

        if($config->element && !isset(self::$_loaded[$signature]))
        {
            $html .= $this->select2(array('element' => false));
            $html .= '<script>
            kQuery(function($){
                $("'.$config->element.'").koowaSelect2('.$options.');
                $("'.$config->element.'").koowaSelect2(\'container\').removeClass(\'required\');
            });</script>';

            self::$_loaded[$signature] = true;
        }

        return $html;
    }

    /**
     * Loads the Koowa customized jQtree behavior and renders a sidebar-nav list useful in split views
     *
     * @see    http://mbraak.github.io/jqTree/
     *
     * @note   If no 'element' option is passed, then only assets will be loaded.
     *
     * @param  array|KObjectConfig $config
     * @throws InvalidArgumentException
     * @return string    The html output
     */
    public function tree($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'   => JFactory::getApplication()->getCfg('debug'),
            'element' => '',
            'selected'  => '',
            'list'    => array()
        ))->append(array(
                'options' => array(
                    'selected' => $config->selected
                )
            ));

        $html = '';

        /**
         * Loading the assets, if not already loaded
         */
        if (!isset(self::$_loaded['tree']))
        {
            $html .= $this->jquery();
            $html .= '<script src="media://koowa/com_koowa/js/tree.jquery'.($config->debug ? '' : '.min').'.js" />';
            $html .= '<script src="media://koowa/com_koowa/js/koowa.tree'.($config->debug ? '' : '.min').'.js" />';

            self::$_loaded['tree'] = true;
        }

        if (!isset(self::$_loaded['tree-css']))
        {
            $html .= '<style src="media://koowa/com_koowa/css/tree.css" />';

            self::$_loaded['tree-css'] = true;
        }

        /**
         * Parse and validate list data, if any. And load the inline js that initiates the tree on specified html element
         */
        $signature = md5('tree-'.$config->element);
        if($config->element && !isset(self::$_loaded[$signature]))
        {
            /**
             * If there's a list set, but no 'data' in the js options, parse it
             */
            if(isset($config->list) && !isset($config->options->data))
            {
                $data = array();
                foreach($config->list as $item)
                {
                    $parts = explode('/', $item->path);
                    array_pop($parts); // remove current id
                    $data[] = array(
                        'label'  => $item->title,
                        'id'     => (int)$item->id,
                        'level'  => (int)$item->level,
                        'path'   => $item->path,
                        'parent' => (int)array_pop($parts)
                    );
                }
                $config->options->append(array('data' => $data));
            }
            /**
             * Validate that the data is the right format
             */
            elseif(isset($config->options->data, $config->options->data[0]))
            {
                $data     = $config->options->data[0];
                $required = array('label', 'id', 'level', 'path', 'parent');
                foreach($required as $key)
                {
                    if(!isset($data[$key])) {
                        throw new InvalidArgumentException('Data must contain required param: '.$key);
                    }
                }
            }

            $options = $config->options;

            $html .= '<script>
            kQuery(function($){
                new Koowa.Tree('.json_encode($config->element).', '.$options.');
            });</script>';

            self::$_loaded[$signature] = true;
        }

        return $html;
    }
}
