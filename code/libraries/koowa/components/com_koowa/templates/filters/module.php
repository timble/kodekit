<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Module Template Filter
 *
 * This filter allow to dynamically inject data into module position.
 *
 * Filter will parse elements of the form <ktml:module position="[position]">[content]</ktml:module> and prepend or
 * append the content to the module position.
 *
 * Filter will parse elements of the form <html:module position="[position]">[content]</module> and inject the
 * content into the module position.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateFilterModule extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
	 * Find any <module></module> elements and inject them into the JDocument object
	 *
	 * @param string $text Block of text to parse
	 * @return ComKoowaTemplateFilterModule
	 */
    public function write(&$text)
    {
        $this->_parseModuleTags($text);
        $this->_parseModulesTags($text);

		return $this;
    }

    /**
     * Parse <ktml:module></ktml:module> tags
     *
     * @param string $text Block of text to parse
     */
    protected function _parseModuleTags(&$text)
    {
        $matches = array();

        if(preg_match_all('#<ktml:module([^>]*)>(.*)</ktml:module>#siU', $text, $matches))
        {
            foreach($matches[0] as $key => $match)
            {
                //Create attributes array
                $attributes = array(
                    'style' 	=> 'component',
                    'params'	=> '',
                    'title'		=> '',
                    'class'		=> '',
                    'prepend'   => true
                );

                $attributes = array_merge($attributes, $this->parseAttributes($matches[1][$key]));

                //Create module object
                $module   	       = new stdClass();
                $module->id        = uniqid();
                $module->content   = $matches[2][$key];
                $module->position  = $attributes['position'];
                $module->params    = $attributes['params'];
                $module->showtitle = !empty($attributes['title']);
                $module->title     = $attributes['title'];
                $module->attribs   = $attributes;
                $module->user      = 0;
                $module->module    = 'mod_dynamic';

                $modules = &ComKoowaModuleHelper::getModules();

                if($module->attribs['prepend']) {
                    array_push($modules, $module);
                } else {
                    array_unshift($modules, $module);
                }
            }

            //Remove the <khtml:module></khtml:module> tags
            $text = str_replace($matches[0], '', $text);
        }
    }

    /**
     * Parse <ktml:modules> and <ktml:modules></ktml:modules> tags
     *
     * @param string $text Block of text to parse
     */
    protected function _parseModulesTags(&$text)
    {
        $replace = array();
        $matches = array();

        $replace = array();
        $matches = array();
        // <ktml:modules position="[position]"></khtml:modules>
        if(preg_match_all('#<ktml:modules\s+position="([^"]+)"(.*)>(.*)</ktml:modules>#siU', $text, $matches))
        {
            $count = count($matches[1]);

            for($i = 0; $i < $count; $i++)
            {
                $position    = $matches[1][$i];
                $attribs     = $this->parseAttributes( $matches[2][$i] );

                $modules = &ComKoowaModuleHelper::getModules($position);
                $replace[$i] = $this->_renderModules($modules, $attribs);

                if(!empty($replace[$i])) {
                    $replace[$i] = str_replace('<ktml:modules:content>', $replace[$i], $matches[3][$i]);
                }
            }

            $text = str_replace($matches[0], $replace, $text);
        }

        // <ktml:modules position="[position]">
        if(preg_match_all('#<ktml:modules\s+position="([^"]+)"(.*)>#iU', $text, $matches))
        {
            $count = count($matches[1]);

            for($i = 0; $i < $count; $i++)
            {
                $position    = $matches[1][$i];
                $attribs     = $this->parseAttributes( $matches[2][$i] );

                $modules = &ComKoowaModuleHelper::getModules($position);
                $replace[$i] = $this->_renderModules($modules, $attribs);
            }

            $text = str_replace($matches[0], $replace, $text);
        }
    }

    /**
     * Render the modules
     *
     * @param object $module    The module object
     * @param array  $attribs   Module attributes
     * @return string  The rendered modules
     */
    public function _renderModules($modules, $attribs = array())
    {
        $html  = '';
        $count = 1;
        foreach($modules as $module)
        {
            //Set the chrome styles
            if(isset($attribs['style'])) {
                $module->chrome  = explode(' ', $attribs['style']);
            }

            //Set the module attributes
            if($count == 1) {
                $attribs['rel']['first'] = 'first';
            }

            if($count == count($modules)) {
                $attribs['rel']['last'] = 'last';
            }

            if(!isset($module->attribs)) {
                $module->attribs = $attribs;
            } else {
                $module->attribs = array_merge($module->attribs, $attribs);
            }

            //Render the module
            $content = ComKoowaModuleHelper::renderModule($module, $attribs);

            //Prepend or append the module
            if(isset($module->attribs['prepend']) && $module->attribs['prepend']) {
                $html = $content.$html;
            } else {
                $html = $html.$content;
            }

            $count++;
        }

        return $html;
    }
}

/**
 * Modules Helper
 *
 * This is a specialised modules helper which gives access to the Joomla modules by reference
.*
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaModuleHelper extends JModuleHelper
{
    public static function &getModules($position = null)
    {
        if($position) {
            $modules =& JModuleHelper::getModules($position);
        } else {
            $modules =& JModuleHelper::_load();
        }

        return $modules;
    }
}



