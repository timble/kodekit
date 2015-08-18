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
 * @package Koowa\Library\Template\Helper
 */
class KTemplateHelperBehavior extends KTemplateHelperAbstract
{
    /**
     * Array which holds a list of loaded Javascript libraries
     *
     * @type array
     */
    protected static $_loaded = array();

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
            'debug' => false
        ));

        $html = $this->jquery();

        if (!isset(self::$_loaded['koowa']))
        {
            $html .= '<ktml:script src="media://koowa/com_koowa/js/koowa'.($config->debug ? '' : '.min').'.js" />';

            self::$_loaded['koowa'] = true;
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
     * @param array|KObjectConfig $config
     * @return string
     */
    public function jquery($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false
        ));

        $html = '';

        if (!isset(self::$_loaded['jquery']))
        {
            $html .= '<ktml:script src="media://koowa/com_koowa/js/jquery'.($config->debug ? '' : '.min').'.js" />';

            self::$_loaded['jquery'] = true;
        }

        return $html;
    }

    /**
     * Render a modal box
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function modal($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'    => false,
            'selector' => '.koowa-modal',
            'data'     => 'koowa-modal',
            'options'  => array('type' => 'image')
        ));

        $html = '';

        if(!isset(self::$_loaded['modal']))
        {
            $html .= $this->jquery();
            $html .= '<ktml:script src="media://koowa/com_koowa/js/jquery.magnific-popup'.($config->debug ? '' : '.min').'.js" />';

            self::$_loaded['modal'] = true;
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
     * Renders an overlay
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function overlay($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'url'       => '',
            'options'   => array(),
            'attribs'   => array(),
        ));

        $translator = $this->getObject('translator');

        $html = '';
        // Load the necessary files if they haven't yet been loaded
        if (!isset(self::$_loaded['overlay']))
        {
            $html .= $this->koowa();
            $html .= '<ktml:script src="media://koowa/com_koowa/js/koowa.overlay.js" />';

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

        $url = $this->getObject('lib:http.url', array('url' => $config->url));

        if(!isset($url->query['format'])) {
            $url->query['format'] = 'overlay';
        }

        $attribs = $this->buildAttributes($config->attribs);

        $id = 'overlay'.mt_rand();
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

        $html .= '<div class="-koowa-overlay" id="'.$id.'" '.$attribs.'><div class="-koowa-overlay-status">'.$translator->translate('Loading...').'</div></div>';
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
            'debug' => false,
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

            $html .= '<ktml:script src="media://koowa/com_koowa/js/jquery.validate'.($config->debug ? '' : '.min').'.js" />';
            $html .= '<ktml:script src="media://koowa/com_koowa/js/patch.validator.js" />';

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
     * @return string   The html output
     */
    public function select2($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'cleanup' => false,
            'debug'   => false,
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
            $html .= '<ktml:script src="media://koowa/com_koowa/js/select2'.($config->debug ? '' : '.min').'.js" />';
            $html .= '<ktml:script src="media://koowa/com_koowa/js/koowa.select2.js" />';

            self::$_loaded['select2'] = true;
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
     * @return string   The html output
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
        ))->append(array(
            'options' => array(
                'label' => $config->text
            )
        ));

        $html ='';

        if (!$config->options->url instanceof KHttpUrl) {
            $config->options->url = $this->getObject('lib:http.url', array('url' => $config->options->url));
        }

        $config->options->url->setQuery(array('fields' => $config->value.','.$config->text), true);

        $config->options->url = (string)$config->options->url;

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
            'debug'   => false,
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
            $html .= $this->koowa();
            $html .= '<ktml:script src="media://koowa/com_koowa/js/jqtree'.($config->debug ? '' : '.min').'.js" />';
            $html .= '<ktml:script src="media://koowa/com_koowa/js/koowa.tree'.($config->debug ? '' : '.min').'.js" />';

            self::$_loaded['tree'] = true;
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

    /**
     * Returns an array of month names (short and long) translated to the site language
     *
     * JavaScript Date object does not have a public API to do this.
     *
     * @param array $config
     * @return string
     */
    public function local_dates($config = array())
    {
        $html   = '';
        $months = array();

        $translator = $this->getObject('translator');

        for ($i = 1; $i < 13; $i++)
        {
            $month  = strtoupper(date('F', mktime(0, 0, 0, $i, 1, 2000)));
            $long  = $translator->translate($month);
            $short = $translator->translate($month.'_SHORT');

            if (strpos($short, '_SHORT') !== false) {
                $short = $long;
            }

            $months[$i] = array('long' => $long, 'short' => $short);
        }

        if (!isset(self::$_loaded['local_dates']))
        {
            $html = sprintf("
            <script>
            if(!Koowa) {
                var Koowa = {};
            }

            if (!Koowa.Date) {
                Koowa.Date = {};
            }

            Koowa.Date.local_month_names = %s;
            Koowa.Date.getMonthName = function(month, short) {
                month = parseInt(month, 10);

                if (month < 1 || month > 12) {
                    throw 'Month index should be between 1 and 12';
                }

                return Koowa.Date.local_month_names[month][short ? 'short' : 'long'];
            };
            </script>
            ", json_encode($months));

            self::$_loaded['local_dates'] = true;
        }

        return $html;
    }
}
