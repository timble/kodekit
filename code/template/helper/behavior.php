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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/kodekit'.($config->debug ? '' : '.min').'.js']);

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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/vue'.($config->debug ? '' : '.min').'.js']);

            static::setLoaded('vue');
        }

        if ($config->entity instanceof ModelEntityInterface)
        {
            $entity = $config->entity->toArray();
            $entity = is_numeric(key($entity)) ? current($entity) : $entity;
            $entity['_isNew'] = $config->entity->isNew();
            $entity['_name']  = StringInflector::singularize($config->entity->getIdentifier()->name);

            $html .= $this->kodekit($config);
            $html .= $this->_onDomReady("
                var form = $('.k-js-form-controller');
                
                if (form.length) {
                    form.data('controller').store = Kodekit.EntityStore.create({
                        form: form,
                        entity: ".json_encode($entity)."
                    });
                }
            ");
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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/modernizr'.($config->debug ? '' : '.min').'.js']);

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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/debugger'.($config->debug ? '' : '.min').'.js']);
            $html .= $this->buildElement('ktml:style', ['src' => 'assets://css/debugger'.($config->debug ? '' : '.min').'.css']);

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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/jquery'.($config->debug ? '' : '.min').'.js']);

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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/bootstrap'.($config->debug ? '' : '.min').'.js']);

            static::setLoaded('bootstrap-javascript');
        }

        if ($config->css && !static::isLoaded('bootstrap-css'))
        {
            $html .= $this->buildElement('ktml:style', ['src' => 'assets://css/bootstrap'.($config->debug ? '' : '.min').'.css']);

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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/jquery.magnific-popup'.($config->debug ? '' : '.min').'.js']);

            static::setLoaded('modal');
        }

        if(!static::isLoaded('modal-select2-fix'))
        {
            // WORKAROUND FOR ISSUE: #873
            $html .= $this->_onDomReady("
                $.magnificPopup.instance._onFocusIn = function(e)
                {
                    // Do nothing if target element is select2 input
                    if( $(e.target).hasClass('select2-search__field') ) {
                        return true;
                    }
        
                    // Else call parent method
                    $.magnificPopup.proto._onFocusIn.call(this,e);
                };");

            static::setLoaded('modal-select2-fix');
        }

        $options   = (string)$config->options;
        $signature = md5('modal-'.$config->selector.$config->options_callback.$options);

        if (!static::isLoaded($signature))
        {
            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= $this->_onDomReady("
                $('$config->selector').each(function(idx, el) {
                    var el = $(el);
                    var data = el.data('$config->data');
                    var options = ".$options.";
                    if (data) {
                        $.extend(true, options, data);
                    }
                    el.magnificPopup(options);
                });");

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
            $html .= $this->buildElement('script', [], "
                (function($){
                    var refresh = '" . $refresh . "';
                    setInterval(function() {
                        $.ajax({
                            url: $url,
                            method: 'HEAD',
                            cache: false
                        })
                    }, refresh * 1000);
                })(kQuery);
            ");

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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/jquery.validate'.($config->debug ? '' : '.min').'.js']);

            static::setLoaded('validator');
        }

        $options   = (string) $config->options;
        $signature = md5('validator-'.$config->selector.$config->options_callback.$options);

        if (!static::isLoaded($signature))
        {
            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= $this->_onDomReady("
                $('$config->selector').on('k:validate', function(event){
                    if(!$(this).valid() || $(this).validate().pendingRequest !== 0) {
                        event.preventDefault();
                    }
                }).validate($options);");

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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/kodekit.select2'.($config->debug ? '' : '.min').'.js']);

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
            'debug' => \Kodekit::getInstance()->isDebug(),
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
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/kodekit.tree'.($config->debug ? '' : '.min').'.js']);

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
            'debug' => \Kodekit::getInstance()->isDebug(),
            'selector' => '[data-k-tooltip]',
            'data'     => 'k-tooltip',
            'options_callback' => null,
            'options'  => array()
        ));

        $html = '';

        // Load Bootstrap with JS plugins.
        if(!static::isLoaded('tooltip'))
        {
            $html .= $this->buildElement('ktml:script', ['src' => 'assets://js/tooltip'.($config->debug ? '' : '.min').'.js']);

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
        $config->append([
            'timezone'  => 'USER_UTC',
            'user_timezone'    => $this->getObject('user')->getTimezone(),
            'server_timezone'  => date_default_timezone_get(),
            'offset' => 0,
            'value'   => 'now',
            'name'    => '',
            'attribs' => [],
            'type'  => 'datetime', // date, time, or datetime (default)
        ])->append([
            'selector' => 'input[name="'.$config->name.'"]'
        ]);

        $has_time = $config->type !== 'date';
        $value = $config->value;
        $offset = $config->offset;
        $timezone = $config->timezone;
        $html = '';

        if ($has_time && $timezone && !$offset)
        {
            if (strtoupper($timezone) === 'SERVER_UTC') {
                $timezone = $config->server_timezone;
            }
            else if (strtoupper($timezone) === 'USER_UTC') {
                $timezone = $config->user_timezone ?: $config->server_timezone;
            }

            $timezone = $timezone instanceof \DateTimeZone ? $timezone : new \DateTimeZone($timezone);
            $offset = $timezone->getOffset(new \DateTime());

            if ($offset)
            {
                $key = 'calendar-triggers-'.$config->selector;

                if (!static::isLoaded($key)) {
                    static::setLoaded($key);

                    $html .= $this->_onDomReady("
                    $('.k-js-form-controller').on('k:submit', function() {
                        var element = document.querySelector('".$config->selector."'),
                            offset  = $offset;
    
                        if (element.value) {
                            var date = new Date(Date.parse(element.value.replace(/[-]/g,'/')));
                            date.setSeconds(date.getSeconds() - offset);
                            element.value = new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 19).replace('T', ' ');
                        }
                    });");
                }
            }
        }

        if ($value && $value != '0000-00-00 00:00:00' && $value != '0000-00-00')
        {
            if (strtoupper($value) == 'NOW') {
                $value = strftime('%Y-%m-%d %H:%M:%S');
            }

            $date = new \DateTime($value, new \DateTimeZone('UTC'));

            if ($offset) {
                if ($offset > 0) {
                    $date = $date->add(new \DateInterval('PT'.$offset.'S'));
                } else {
                    $offset = -1*$offset;
                    $date = $date->sub(new \DateInterval('PT'.$offset.'S'));
                }
            }

            $value = $date->format('Y-m-d H:i:s');
        } else {
            $value = '';
        }

        $selectedDate = substr($value, 0, 10);
        $selectedTime = substr($value, 11);

        $date_attributes = array_merge([
            'class' => 'k-form-control', 'type' => 'date', 'name' => $config->name, 'value' => $selectedDate,
            'pattern' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'
        ], ObjectConfig::unbox($config->attribs));

        $time_attributes = array_merge([
            'class' => 'k-form-control', 'type' => 'time', 'name' => $config->name, 'value' => $selectedTime,
            'pattern' => '[0-9]{2}:[0-9]{2}:[0-9]{2}'
        ], ObjectConfig::unbox($config->attribs));

        if ($config->type === 'date') {
            $html .= $this->buildElement('input', $date_attributes);
        } elseif ($config->type === 'time') {
            $html .= $this->buildElement('input', $time_attributes);
        } else {
            $hidden_attributes = array_merge([
                'type' => 'hidden', 'name' => $config->name, 'value' => $value,
            ], ObjectConfig::unbox($config->attribs));

            unset($date_attributes['name']);
            unset($time_attributes['name']);
            unset($date_attributes['id']);
            unset($time_attributes['id']);
            unset($time_attributes['x-ref']);
            unset($time_attributes['x-ref']);

            $html .= $this->buildElement('div', ['class' => 'k-input-group k-js-datetime-group'],
                $this->buildElement('input', $hidden_attributes)
                . $this->buildElement('input', $date_attributes)
                . $this->buildElement('input', $time_attributes)
            );

            if (!static::isLoaded('calendar-combiner')) {
                static::setLoaded('calendar-combiner');

                $html .= <<<SCRIPT
<script data-inline>
document.addEventListener('input', function(e) {
    for (var target = e.target; target && target !== this; target = target.parentNode) {
        if (target.matches(".k-js-datetime-group")) {
            var hiddenElement = target.querySelector("input[type=\"hidden\"]");
            
            if (hiddenElement) {
                var dateElement = target.querySelector("input[type=\"date\"]");
                var timeElement = target.querySelector("input[type=\"time\"]");
                
                hiddenElement.value = dateElement.value+" "+timeElement.value;
            }
            
            break;
        }
    }
}, false);</script>
SCRIPT;
            }
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
            $html .= $this->buildElement('ktml:script', [
                'src' => 'assets://js/alpine'.($config->debug ? '' : '.min').'.js',
                'type' => 'module'
            ]);

            if (\Kodekit::getInstance()->isDebug())
            {
                $html .= '
                <script>
                    window.addEventListener("DOMContentLoaded", function()
                    {
                        let el = document.body.querySelector("section[x-data]");
                       
                        window.kAlpine = (el._x_dataStack.length > 1) ? el._x_dataStack : el._x_dataStack[0];
                    });
                </script>';
            }


            static::setLoaded('folikit.alpine');
        }

        return $html;
    }
}
