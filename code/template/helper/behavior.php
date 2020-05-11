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
 * Behavior Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperBehavior extends TemplateHelperAbstract
{
    /**
     * Array which holds a list of loaded Javascript libraries
     *
     * @type array
     */
    protected static $_loaded = array();

    /**
     * Marks the resource as loaded
     *
     * @param      $key
     * @param bool $value
     */
    public static function setLoaded($key, $value = true)
    {
        static::$_loaded[$key] = $value;
    }

    /**
     * Checks if the resource is loaded
     *
     * @param $key
     * @return bool
     */
    public static function isLoaded($key)
    {
        return !empty(static::$_loaded[$key]);
    }

    protected function _onDomReady($code) {
        return '<script>
            kQuery(function($) {
                '.$code.'
            });
        </script>';
    }

    /**
     * Loads kodekit.js
     *
     * @param array|ObjectConfig $config
     * @return string
     */
    public function kodekit($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug(),
        ));

        $html = '';

        if (!static::isLoaded('kodekit'))
        {
            $html .= $this->jquery($config);
            $html .= '<ktml:script src="assets://js/kodekit'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('kodekit');
        }

        return $html;
    }


    /**
     * Loads Vue.js and optionally Vuex
     * @param array $config
     * @return string
     */
    public function vue($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append([
            'debug'  => \Kodekit::getInstance()->isDebug(),
            'vuex'   => true,
            'entity' => null
        ]);

        $html = '';

        if (!static::isLoaded('vue'))
        {
            $html .= '<ktml:script src="assets://js/vue'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('vue');
        }

        if ($config->entity instanceof ModelEntityInterface)
        {
            $entity = $config->entity->toArray();
            $entity = is_numeric(key($entity)) ? current($entity) : $entity;
            $entity['_isNew'] = $config->entity->isNew();
            $entity['_name']  = StringInflector::singularize($config->entity->getIdentifier()->name);

            $html .= $this->kodekit($config);
            $html .= "
            <script>
                kQuery(function($) {
                    var form = $('.k-js-form-controller');
                    
                    if (form.length) {
                        form.data('controller').store = Kodekit.EntityStore.create({
                            form: form,
                            entity: ".json_encode($entity)."
                        });
                    }
                });
            </script>
";
        }

        return $html;
    }

    /**
     * Loads Modernizr
     *
     * @param array|ObjectConfig $config
     * @return string
     */
    public function modernizr($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug()
        ));

        $html = '';

        if (!static::isLoaded('modernizr'))
        {
            $html = '<ktml:script src="assets://js/modernizr'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('modernizr');
        }

        return $html;
    }

    public function debugger($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug()
        ));

        $html = '';

        if (!static::isLoaded('debugger'))
        {
            $html = '<ktml:script src="assets://js/debugger'.($config->debug ? '' : '.min').'.js" />';
            $html = '<ktml:style src="assets://css/debugger'.($config->debug ? '' : '.min').'.css" />';

            static::setLoaded('debugger');
        }

        return $html;
    }

    /**
     * Loads jQuery under a global variable called kQuery.
     *
     * If debug config property is set, an uncompressed version will be included.
     *
     * You can do window.jQuery = window.$ = window.kQuery; to use the default names
     *
     * @param array|ObjectConfig $config
     * @return string
     */
    public function jquery($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug(),
        ));

        $html = '';

        if (!static::isLoaded('jquery'))
        {
            $html .= '<ktml:script src="assets://js/jquery'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('jquery');
        }

        return $html;
    }

    /**
     * Add Bootstrap JS and CSS a modal box
     *
     * @param array|ObjectConfig $config
     * @return string   The html output
     */
    public function bootstrap($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug' => \Kodekit::getInstance()->isDebug(),
            'css'   => true,
            'javascript' => false
        ));

        $html = '';

        if ($config->javascript && !static::isLoaded('bootstrap-javascript'))
        {
            $html .= $this->jquery($config);
            $html .= '<ktml:script src="assets://js/bootstrap'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('bootstrap-javascript');
        }

        if ($config->css && !static::isLoaded('bootstrap-css'))
        {
            $html .= '<ktml:style src="assets://css/bootstrap.css" />';

            static::setLoaded('bootstrap-css');
        }

        return $html;
    }

    /**
     * Render a modal box
     *
     * @param array|ObjectConfig $config
     * @return string   The html output
     */
    public function modal($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug'    => \Kodekit::getInstance()->isDebug(),
            'selector' => '[data-k-modal]',
            'data'     => 'k-modal',
            'options_callback' => null,
            'options'  => array('type' => 'image')
        ));

        $html = '';

        if(!static::isLoaded('modal'))
        {
            $html .= $this->jquery($config);
            $html .= '<ktml:script src="assets://js/jquery.magnific-popup'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('modal');
        }

        if(!static::isLoaded('modal-select2-fix'))
        {
            $html .= "<script>

                // WORKAROUND FOR ISSUE: #873

                kQuery(function($)
                {
                    $.magnificPopup.instance._onFocusIn = function(e)
                    {
                        // Do nothing if target element is select2 input
                        if( $(e.target).hasClass('select2-search__field') ) {
                            return true;
                        }
            
                        // Else call parent method
                        $.magnificPopup.proto._onFocusIn.call(this,e);
                    };
                });
            </script>";

            static::setLoaded('modal-select2-fix');
        }

        $options   = (string)$config->options;
        $signature = md5('modal-'.$config->selector.$config->options_callback.$options);

        if (!static::isLoaded($signature))
        {
            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= "<script>
            kQuery(function($){
                $('$config->selector').each(function(idx, el) {
                    var el = $(el);
                    var data = el.data('$config->data');
                    var options = ".$options.";
                    if (data) {
                        $.extend(true, options, data);
                    }
                    el.magnificPopup(options);
                });
            });
            </script>";

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Keep session alive
     *
     * This will send an ascynchronous request to the server via AJAX on an interval in secs
     *
     * @param   array   $config An optional array with configuration options
     * @return string    The html output
     */
    public function keepalive($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'refresh' => 15 * 60, //default refresh is 15min
            'url'     => '',      //default to window.location.url
        ));

        $html = '';

        // Only load once
        if (!static::isLoaded('keepalive'))
        {
            $session = $this->getObject('user')->getSession();
            if($session->isActive())
            {
                //Get the config session lifetime
                $lifetime = $session->getLifetime();

                //Refresh time is 1 minute less than the lifetime
                $refresh =  ($lifetime <= 60) ? 30 : $lifetime - 60;
            }
            else $refresh = (int) $config->refresh;

            // Longest refresh period is one hour to prevent integer overflow.
            if ($refresh > 3600 || $refresh <= 0) {
                $refresh = 3600;
            }

            if(empty($config->url)) {
                $url = 'window.location.url';
            } else {
                $url = "'.$config->url.'";
            }

            // Build the keep alive script.
            $html  = $this->jquery($config);
            $html .=
                "<script>
                (function($){
                    var refresh = '" . $refresh . "';
                    setInterval(function() {
                        $.ajax({
                            url: $url,
                            method: 'HEAD',
                            cache: false
                        })
                    }, refresh * 1000);
                })(kQuery);</script>";

            static::setLoaded('keepalive');
        }
        return $html;
    }

    /**
     * Loads the Forms.Validator class and connects it to Kodekit.Controller.Form
     *
     * @param array|ObjectConfig $config
     * @return string   The html output
     */
    public function validator($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug'    => \Kodekit::getInstance()->isDebug(),
            'selector' => '.k-js-form-controller',
            'options_callback' => null,
            'options'  => array(
                'ignoreTitle' => true,
                'onsubmit'    => false // We run the validation ourselves
            )
        ));

        $html = '';

        if(!static::isLoaded('validator'))
        {
            $html .= $this->kodekit();
            $html .= '<ktml:script src="assets://js/jquery.validate'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('validator');
        }

        $options   = (string) $config->options;
        $signature = md5('validator-'.$config->selector.$config->options_callback.$options);

        if (!static::isLoaded($signature))
        {
            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= "<script>
            kQuery(function($){
                $('$config->selector').on('k:validate', function(event){
                    if(!$(this).valid() || $(this).validate().pendingRequest !== 0) {
                        event.preventDefault();
                    }
                }).validate($options);
            });
            </script>";

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Loads the select2 behavior and attaches it to a specified element
     *
     * @see    https://select2.github.io
     *
     * @param  array|ObjectConfig $config
     * @return string   The html output
     */
    public function select2($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'cleanup' => false,
            'debug'   => \Kodekit::getInstance()->isDebug(),
            'element' => '.select2-listbox',
            'options_callback' => null, // wraps the call to select2 options in JavaScript, can be used to add JS code
            'options' => array(
                'theme'   => 'bootstrap',
                'width' => 'resolve'
            )
        ));

        $html = '';

        if (!static::isLoaded('select2'))
        {
            $html .= $this->jquery($config);
            $html .= '<ktml:script src="assets://js/kodekit.select2'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('select2');
        }

        $options   = $config->options;
        $signature = md5('select2-'.$config->element.$config->options_callback.$options);

        if($config->element && !static::isLoaded($signature))
        {
            $options = (string) $options;

            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $init = $config->init_callback ? $config->init_callback.'(selector);' : '';
            $html .= $this->_onDomReady('
                var selector = $("'.$config->element.'");                    
                selector.select2('.$options.');
                selector.on("select2:close", function () { $(this).focus(); });
                '.$init."\n");

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Loads the autocomplete behavior and attaches it to a specified element
     *
     * @param  array|ObjectConfig $config
     * @return string   The html output
     */
    public function autocomplete($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'element'          => null,
            'type'             => $config->name,
            'options_callback' => null, // wraps the call to select2 options in JavaScript, can be used to add JS code
            'options'          => array(
                'minimumInputLength' => 2,
                'validate'           => false, //Toggle if the forms validation helper is loaded
                'queryVarName'       => 'search',
                'width'              => 'resolve',
                'name'               => '',
                'model'              => $config->model,
                'placeholder'        => $config->prompt,
                'allowClear'         => $config->deselect,
                'value'              => $config->value,
                'text'               => $config->text,
                'selected'           => $config->selected,
                'url'                => $config->ajax_url,
                'multiple'           => false
            )
        ))->append(array(
            'options' => array(
                'label' => $config->text
            )
        ));

        $html ='';

        if (!$config->options->url instanceof HttpUrl) {
            $config->options->url = $this->getObject('lib:http.url', ['url' => $config->options->url]);
        }

        if(!empty($config->type))
        {
            $config->options->url->setQuery(array('fields['.$config->type.']' => $config->value.','.$config->text), true);
            $config->options->url = (string)$config->options->url;
        }

        $options   = $config->options;
        $signature = md5('autocomplete-'.$config->element.$config->options_callback.$options);

        if($config->element && !static::isLoaded($signature))
        {
            $options = (string) $options;

            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= $this->select2(array('element' => false));

            $init = $config->init_callback ? ($config->init_callback . '(selector);') : '';

            // TODO: test this
            $html .= $this->_onDomReady('var selector = $("'.$config->element.'");
                    selector.select2(Kodekit.getSelect2Options('.$options.'));
                    '.$init."\n");

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Loads the Kodekit customized jQtree behavior and renders a sidebar-nav list useful in split views
     *
     * @see    http://mbraak.github.io/jqTree/
     *
     * @note   If no 'element' option is passed, then only assets will be loaded.
     *
     * @param  array|ObjectConfig $config
     * @throws \InvalidArgumentException
     * @return string    The html output
     */
    public function tree($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug'   => false,
            'element' => '',
            'selected'  => '',
            'list'    => array()
        ))->append(array(
            'options_callback' => null,
            'options' => array(
                'selected' => $config->selected
            )
        ));

        $html = '';

        /**
         * Loading the assets, if not already loaded
         */
        if (!static::isLoaded('tree'))
        {
            $html .= $this->kodekit();
            $html .= '<ktml:script src="assets://js/kodekit.tree'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('tree');
        }

        /**
         * Parse and validate list data, if any. And load the inline js that initiates the tree on specified html element
         */
        $signature = md5('tree-'.$config->element);
        if($config->element && !static::isLoaded($signature))
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
                        throw new \InvalidArgumentException('Data must contain required param: '.$key);
                    }
                }
            }

            $options = (string) $config->options;

            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= $this->_onDomReady('new Kodekit.Tree('.json_encode($config->element).', '.$options.');');

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Render a tooltip
     *
     * @param array|ObjectConfig $config
     * @return string   *The html output
     */
    public function tooltip($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'selector' => '[data-k-tooltip]',
            'data'     => 'k-tooltip',
            'options_callback' => null,
            'options'  => array()
        ));

        $html = '';

        // Load Bootstrap with JS plugins.
        if(!static::isLoaded('tooltip'))
        {
            $html .= '<ktml:script src="assets://js/tooltip'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('tooltip');
        }

        $options = (string) $config->options;

        if ($config->options_callback) {
            $options = $config->options_callback.'('.$options.')';
        }

        $signature = md5('tooltip-'.$config->selector.$options);

        if(!static::isLoaded($signature))
        {
            $html .= $this->_onDomReady("$('$config->selector').each(function(idx, el) {
                        var el = $(el);
                        var data = el.data('$config->data');
                        var options = ".$options.";
                        if (data) {
                            $.extend(true, options, data);
                        }
                        el.ktooltip(options);
                    });");

            static::setLoaded($signature);

        }

        return $html;
    }


    /**
     * Loads the calendar behavior and attaches it to a specified element
     *
     * @param array|ObjectConfig $config
     * @return string   The html output
     */
    public function calendar($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'debug'   => \Kodekit::getInstance()->isDebug(),
            'timezone'  => 'USER_UTC',
            'user_timezone'    => $this->getObject('user')->getTimezone(),
            'server_timezone'  => date_default_timezone_get(),
            'offset_seconds' => 0,
            'value'   => gmdate("M d Y H:i:s"),
            'name'    => '',
            'format'  => '%Y-%m-%d %H:%M:%S',
            'first_week_day' => 0,
            'attribs'        => array(
                'size'        => 25,
                'maxlength'   => 19,
                'placeholder' => ''
            )
        ))->append(array(
            'id'      => 'datepicker-'.$config->name,
            'options_callback' => null,
            'options' => array(
                'todayBtn' => false,
                'clearBtn' => false,
                'language' => 'en-GB',
                'autoclose' => true,
            )
        ));

        if ($config->timezone && !$config->offset_seconds)
        {
            if (strtoupper($config->timezone) === 'SERVER_UTC') {
                $config->timezone = $config->server_timezone;
            }
            else if (strtoupper($config->timezone) === 'USER_UTC') {
                $config->timezone = $config->user_timezone ?: $config->server_timezone;
            }

            $timezone               = new \DateTimeZone($config->timezone);
            $config->offset_seconds = $timezone->getOffset(new \DateTime());
        }

        if ($config->value && $config->value != '0000-00-00 00:00:00' && $config->value != '0000-00-00')
        {
            if (strtoupper($config->value) == 'NOW') {
                $config->value = strftime($config->format);
            }

            $date = new \DateTime($config->value, new \DateTimeZone('UTC'));

            $config->value = gmstrftime($config->format, ((int)$date->format('U')) + $config->offset_seconds);
        } else {
            $config->value = '';
        }

        $attribs = $this->buildAttributes($config->attribs);
        $value   = StringEscaper::attr($config->value);

        if ($config->attribs->readonly === 'readonly' || $config->attribs->disabled === 'disabled')
        {
            $html  = '<div>';
            $html .= '<input type="text" name="'.$config->name.'" id="'.$config->id.'" value="'.$value.'" '.$attribs.' />';
            $html .= '</div>';
        }
        else
        {
            $html = $this->_loadCalendarScripts($config);

            if (!isset(self::$_loaded['calendar-triggers'])) {
                self::$_loaded['calendar-triggers'] = array();
            }

            // Only display the triggers once for each control.
            if (!in_array($config->id, self::$_loaded['calendar-triggers']))
            {
                $options = (string) $config->options;

                if ($config->options_callback) {
                    $options = $config->options_callback.'('.$options.')';
                }

                $html .= $this->_onDomReady("$('#".$config->id."').kdatepicker(".$options.");");

                if ($config->offset_seconds)
                {
                    $html .= $this->_onDomReady("
                        $('.k-js-form-controller').on('k:submit', function() {
                            var element = kQuery('#".$config->id."'),
                                picker  = element.data('kdatepicker'),
                                offset  = $config->offset_seconds;

                            if (picker && element.children('input').val()) {
                                picker.setDate(new Date(picker.getDate().getTime() + (-1*offset*1000)));
                            }
                        });");
                }

                static::setLoaded('calendar-triggers'.$config->id);
            }

            $format = str_replace(
                array('%Y', '%y', '%m', '%d', '%H', '%M', '%S'),
                array('yyyy', 'yy', 'mm', 'dd', 'hh', 'ii', 'ss'),
                $config->format
            );

            $html .= '<div class="k-input-group date" data-date-format="'.$format.'" id="'.$config->id.'">';
            $html .= '<input class="k-form-control" type="text" name="'.$config->name.'" value="'.$value.'"  '.$attribs.' />';
            $html .= '<span class="k-input-group__button input-group-btn">';
            $html .= '<button type="button" class="k-button k-button--default     btn     ">';
            $html .= '<span class="k-icon-calendar" aria-hidden="true"></span>';
            $html .= '<span class="k-visually-hidden">calendar</span>';
            $html .= '</button>';
            $html .= '</span>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param ObjectConfig $config
     * @return string
     */
    protected function _loadCalendarScripts(ObjectConfig $config)
    {
        $html = '';

        if (!static::isLoaded('calendar')) {
            $html .= '<ktml:script src="assets://js/kodekit.datepicker'.($config->debug ? '' : '.min').'.js" />';

            static::setLoaded('calendar');
        }

        if (!static::isLoaded('calendar-locale'))
        {
            $locale = array(
                'days'        => ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                'daysShort'   => ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                'daysMin'     => ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
                'months'      => ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                'monthsShort' => ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            );

            $translator = $this->getObject('translator');

            foreach($locale as $key => $items){
                $locale[$key] = array_map(array($translator, 'translate'), $items);
            }
            $locale['today']     = $translator->translate('Today');
            $locale['clear']     = $translator->translate('Clear');
            $locale['weekStart'] = $config->first_week_day;

            $html .= '<script>
            (function($){
                $.fn.kdatepicker.dates['.json_encode($config->options->language).'] = '.json_encode($locale).';
            }(kQuery));
            </script>';

            static::setLoaded('calendar-locale');
        }

        return $html;
    }

    /**
     * Loads Alpine.js
     *
     * If debug config property is set, an uncompressed version will be included.
     *
     * @param array|ObjectConfig $config
     * @return string
     */
    public function alpine($config = [])
    {
        $config = new ObjectConfigJson($config);
        $config->append([
            'debug' => \Kodekit::getInstance()->isDebug(),
        ]);

        $html = '';

        if (!static::isLoaded('alpine')) {
            $html .= '<ktml:script src="assets://js/alpine'.($config->debug ? '' : '.min').'.js" type="module"  />';
            $html .= '<ktml:script src="assets://js/alpine.ie11'.($config->debug ? '' : '.min').'.js" nomodule defer />';

            static::setLoaded('folikit.alpine');
        }

        return $html;
    }
}
