<?php
/**
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Behavior Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
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
     * @param array|KConfig $config
     *
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
     * @param array|KConfig $config
     *
     * @return string
     */
    public function jquery($config = array())
    {
        $config = new KConfig($config);
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
     * @param array|KConfig $config
     *
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
     * @param array|KConfig $config
     *
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
     * @param array|KConfig $config
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

        JHTML::_('behavior.modal', $config->selector, $config->toArray());

		return '';
	}


    /**
     * Render a tooltip
     *
     * @param array|KConfig $config
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

        JHTML::_('behavior.tooltip', $config->selector, $config->toArray());

        return '';

        return $html;
    }

    /**
     * Loads the calendar behavior and attaches it to a specified element
     *
     * @param array|KConfig $config
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
        ))->append(array(
                'id'      => 'button-'.$config->name,
            ));

        if($config->date && $config->date != '0000-00-00 00:00:00' && $config->date != '0000-00-00') {
            $config->date = strftime($config->format, strtotime($config->date) /*+ $config->gmt_offset*/);
        }
        else $config->date = '';

        if (!isset(self::$_loaded['calendar']))
        {
            JHtml::_('behavior.calendar');

            self::$_loaded['calendar'] = true;
        }


        return JHtml::_('calendar', $config->date, $config->name, $config->id, $config->format = '%Y-%m-%d', KConfig::unbox($config->attribs));
    }

    /**
     * Renders an overlay
     *
     * @param array|KConfig $config
     *
     * @return string
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

        $url = $this->getService('koowa:http.url', array('url' => $config->url));
        if(!isset($url->query['format'])) {
            $url->query['format'] = 'overlay';
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
     * Loads the Forms.Validator class and connects it to Koowa.Controller
     *
     * This allows you to do easy, CSS class based forms validation.
     * Koowa.Controller.Form automatically works with it.
     *
     * @see    http://www.mootools.net/docs/more125/more/Forms/Form.Validator
     *
     * @param array|KConfig $config
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
     * @return string	The html output
     */
    public function select2($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'element' => '.select2-listbox',
            'options' => array(
                'width' => 'resolve',
                'dropdownCssClass' => 'koowa'
            )
        ));

        $html ='';

        if (!isset(self::$_loaded['jquery'])) {
            $html .= $this->jquery();
        }

        if (!isset(self::$_loaded['select2'])) {

            $html .= '<script src="media://koowa/com_koowa/js/select2.js" />';

            $html .= '<script>jQuery(function($){
                $("'.$config->element.'").select2('.$config->options.');
            });</script>';

            if(isset(self::$_loaded['validator']))
            {
                $html .= '<script src="media://koowa/com_koowa/js/select2.validator.js" />';

                $html .= '<script>jQuery(function($){
                    $("'.$config->element.'").select2(\'container\').removeClass(\'required\');
                });</script>';
            }

            self::$_loaded['select2'] = true;
        }

        return $html;
    }

    /**
     * Loads the autocomplete behavior and attaches it to a specified element
     *
     * Dropped/changed params:
     *                  'path' changed to 'text'
     *
     * @param  array|KConfig $config
     * @return string	The html output
     */
    public function autocomplete($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'element' => '.select2-listbox',
            'identifier'    => null,
            //@TODO deprecate path, using same options as listbox helper instead
            'path'          => 'title',
            'filter'		=> array(),
            'validate'		=> true,


            //Shortcut options
            'url'           => null,
            'queryVarName'  => 'search',
            'selected'		=> null,
            'name'          => null,
            'text'          => null,
            'value'         => null,
        ))->append(array(
            'options' => array(
                'text' => $config->text,
                'value' => $config->value,
                'selected' => $config->selected,
                'url'   => $config->url,
                'width' => 'resolve',
                'dropdownCssClass' => 'koowa',
                'path' => $config->path,
                'placeholder' => false,
                'queryVarName' => $config->queryVarName,
                'filter' => KConfig::unbox($config->filter)
            ),
        ));

        $html ='';

        if (!isset(self::$_loaded['jquery'])) {
            $html .= $this->jquery();
        }

        if (!isset(self::$_loaded['select2'])) {

            $html .= '<script src="media://koowa/com_koowa/js/select2.js" />';
            $html .= '<style src="media://koowa/com_koowa/css/select2.css" />';

            self::$_loaded['select2'] = true;
        }

        $html .= '<script>jQuery(function($){
                $("'.$config->element.'").koowaSelect2('.$config->options.');
            });</script>';

        // Load the necessary files if they haven't yet been loaded
        if(!isset(self::$_loaded['autocomplete']))
        {
            $html .= '<script src="media://koowa/com_koowa/js/koowa.select2.js" />';

            if(isset(self::$_loaded['validator']))
            {
                $html .= '<script src="media://koowa/com_koowa/js/select2.validator.js" />';

                $html .= '<script>jQuery(function($){
                    $("'.$config->element.'").koowaSelect2(\'container\').removeClass(\'required\');
                });</script>';
            }

            //@TODO move ajax specific code into separate js file
        }

        return $html;
    }
}
