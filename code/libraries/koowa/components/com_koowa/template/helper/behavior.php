<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Behavior Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperBehavior extends KTemplateHelperBehavior
{
    /**
     * Loads koowa.js
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

        return parent::koowa($config);
    }

    public function modal($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::modal($config);
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
                // Can't use JHtml here as it makes a file_exists call on koowa.kquery.js?version
                $path = JURI::root(true).'/media/koowa/com_koowa/js/koowa.kquery.js?'.substr(md5(Koowa::VERSION), 0, 8);
                JFactory::getDocument()->addScript($path);
            }
            else $html .= parent::jquery($config);

            self::$_loaded['jquery'] = true;
        }

        return $html;
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
     * @return string   The html output
     */
    public function dialog($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'    => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::dialog($config);
    }

    /**
     * Render a tooltip
     *
     * @param array|KObjectConfig $config
     * @return string   *The html output
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
            $identifier         = $this->getIdentifier()->toArray();
            $identifier['name'] = 'bootstrap';
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
     * @return string   The html output
     */
    public function calendar($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'    => JFactory::getApplication()->getCfg('debug'),
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
                 //'orientation' => 'auto left', //popover arrow set to point at the datepicker icon,
                 //'parentEl' => false //this feature breaks if a parent el is position: relative;
             )
        ));

        $translator = $this->getObject('translator');

        // Handle the special case for "now".
        if (strtoupper($config->value) == 'NOW') {
            $config->value = strftime($config->format);
        }

        $html = '';


        if($config->value && $config->value != '0000-00-00 00:00:00' && $config->value != '0000-00-00') {
            $config->value = strftime($config->format, strtotime($config->value));
        } else {
            $config->value = '';
        }

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
                    $date->setTimezone(new DateTimeZone($this->getObject('user')->getParameter('timezone', JFactory::getConfig()->get('offset'))));

                    // Transform the date string.
                    $config->value = $date->format('Y-m-d H:i:s', true, false);
                }
                break;
        }

        if (!isset(self::$_loaded['calendar']))
        {
            $html .= '<ktml:script src="media://koowa/com_koowa/js/datepicker'.($config->debug ? '' : '.min').'.js" />';
            $html .= '<ktml:script src="media://koowa/com_koowa/js/koowa.datepicker.js" />';

            $locale = array(
                'days'  =>  array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
                'daysShort' => array('Sun','Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
                'daysMin' => array('Su','Mo','Tu','We','Th','Fr','Sa','Su'),
                'months' => array('January','February','March','April','May','June','July','August','September','October','November','December'),
                'monthsShort' => array('January_short','February_short','March_short','April_short','May_short','June_short','July_short','August_short','September_short','October_short','November_short','December_short')
            );

            foreach($locale as $key => $item){
                $locale[$key] = array_map(array($translator, 'translate'), $item);
            }
            $locale['today']     = $translator->translate('Today');
            $locale['clear']     = $translator->translate('Clear');
            $locale['weekStart'] = JFactory::getLanguage()->getFirstDay();

            $html .= '<script>
            (function($){
                $.fn.datepicker.dates['.json_encode($config->options->language).'] = '.json_encode($locale).';
            }(kQuery));
            </script>';

            self::$_loaded['calendar'] = true;
        }

        if (!isset(self::$_loaded['calendar-triggers'])) {
            self::$_loaded['calendar-triggers'] = array();
        }

        if ($config->value) {
            $date = new DateTime($config->value);
            $config->value = strftime($config->format, $date->format('U'));
        }

        $attribs = $this->buildAttributes($config->attribs);
        $value   = $this->getTemplate()->escape($config->value);

        // @TODO this is legacy, or bc support, and may not be compatible with strftime and the like
        $config->format = str_replace(
            array('%Y', '%y', '%m', '%d', '%H', '%M', '%S'),
            array('yyyy', 'yy', 'mm', 'dd', 'hh', 'ii', 'ss'),
            $config->format
        );

        if ($config->attribs->readonly !== 'readonly' && $config->attribs->disabled !== 'disabled')
        {
            // Only display the triggers once for each control.
            if (!in_array($config->id, self::$_loaded['calendar-triggers']))
            {
                $html .= "<script>
                    kQuery(function($){
                        $('#".$config->id."').koowaDatepicker(".$config->options.");
                    });
                </script>";
                self::$_loaded['calendar-triggers'][] = $config->id;
            }

            $html .= '<div class="input-group date datepicker" data-date-format="'.$config->format.'" id="'.$config->id.'">';
            $html .= '<input class="input-group-form-control" type="text" name="'.$config->name.'" value="'.$value.'"  '.$attribs.' />';
            $html .= '<span class="input-group-btn">';
            $html .= '<span class="btn" >';
            $html .= '<span class="koowa_icon--calendar"><i>calendar</i></span>';
            $html .= '</span>';
            $html .= '</span>';
            $html .= '</div>';
        }
        else
        {
            $html = '';
            $html .= '<div>';
            $html .= '<input type="text" name="'.$config->name.'" id="'.$config->id.'" value="'.$value.'" '.$attribs.' />';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Loads the Forms.Validator class and connects it to Koowa.Controller.Form
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function validator($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::validator($config);
    }

    /**
     * Loads the select2 behavior and attaches it to a specified element
     *
     * @see    http://ivaynberg.github.io/select2/select-2.1.html
     *
     * @param  array|KObjectConfig $config
     * @return string   The html output
     */
    public function select2($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::select2($config);
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
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::tree($config);
    }
}
