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
 * Template Behavior Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaTemplateHelperBehavior extends KTemplateHelperBehavior
{

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
            $html .= '<script src="media://koowa/library/js/koowa.js" />';
            $html .= '<style src="media://koowa/library/css/koowa.css" />';

            self::$_loaded['koowa'] = true;
        }

        return $html;
    }

    /**
     * Array which holds a list of loaded javascript libraries
     *
     * boolean
     */
    protected static $_loaded = array();

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
                $html .= '<script src="media://koowa/library/js/jquery'.($config->debug ? '' : '.min').'.js" />';
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


        return JHtml::_('behavior.calendar', $config->date, $config->name, $config->id, $config->format = '%Y-%m-%d', KConfig::unbox($config->attribs));
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

            $html .= '<script src="media://koowa/library/js/validator.js" />';
            $html .= '<script src="media://koowa/library/js/patch.validator.js" />';

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
     *
     * @param  array|KConfig $config
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
                $html .= '<script src="media://koowa/library/js/autocomplete-2.0.js" />';
            } else {
                $html .= '<script src="media://koowa/library/js/autocomplete-1.0.js" />';
            }
            $html .= '<script src="media://koowa/library/js/patch.autocomplete.js" />';
            $html .= '<style src="media://koowa/library/css/autocomplete.css" />';
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
}