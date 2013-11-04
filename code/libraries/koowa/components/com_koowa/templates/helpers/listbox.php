<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Listbox Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperListbox extends ComKoowaTemplateHelperSelect
{
    /**
     * Provides a users select box.
     *
     * You have to create a user controller to use autocomplete.
     * Autocomplete is highly recommended since a site with 10k users can make you run into memory limit issues.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @return string The autocomplete users select box.
     */
    public function users($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'autocomplete' => true,
            'model'        => 'users',
            'name'         => 'user',
            'value'        => 'id',
            'text'         => 'name',
            'sort'         => 'name',
            'validate'     => false
        ));

        return $this->_listbox($config);
    }

    /**
     * Generates an HTML enabled listbox
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function enabled( $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'      => 'enabled',
            'attribs'   => array(),
            'deselect'  => true,
            'prompt'    => '- '.$this->translate('Select').' -',
        ))->append(array(
            'selected'  => $config->{$config->name}
        ));

        $options = array();

        if($config->deselect) {
            $options[] = $this->option(array('text' => $config->prompt, 'value' => ''));
        }

        $options[] = $this->option(array('text' => $this->translate( 'Enabled' ) , 'value' => 1 ));
        $options[] = $this->option(array('text' => $this->translate( 'Disabled' ), 'value' => 0 ));

        //Add the options to the config object
        $config->options = $options;

        return $this->optionlist($config);
    }

    /**
     * Generates an HTML published listbox
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function published($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'      => 'enabled',
            'attribs'   => array(),
            'deselect'  => true,
            'prompt'    => '- '.$this->translate('Select').' -'
        ))->append(array(
            'selected'  => $config->{$config->name}
        ));
    
        $options = array();
    
        if ($config->deselect) {
            $options[] = $this->option(array('text' => $config->prompt, 'value' => ''));
        }
    
        $options[] = $this->option(array('text' => $this->translate('Published'), 'value' => 1 ));
        $options[] = $this->option(array('text' => $this->translate('Unpublished') , 'value' => 0 ));
    
        //Add the options to the config object
        $config->options = $options;
    
        return $this->optionlist($config);
    }

    /**
     * Generates an HTML access listbox
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function access($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'      => 'access',
            'attribs'   => array(),
            'deselect'  => true,
            'prompt'    => '- '.$this->translate('Select').' -'
        ))->append(array(
            'selected'  => $config->{$config->name}
        ));

        $prompt = false;
        if ($config->deselect) {
            $prompt = array((object) array('value' => '', 'text'  => $config->prompt));
        }

        $html = JHtml::_('access.level', $config->name, $config->selected, $config->attribs->toArray(), $prompt);
    
        return $html;
    }

    /**
     * Generates an HTML optionlist based on the distinct data from a model column.
     *
     * The column used will be defined by the name -> value => column options in
     * cascading order.
     *
     * If no 'model' name is specified the model identifier will be created using
     * the helper identifier. The model name will be the pluralised package name.
     *
     * If no 'value' option is specified the 'name' option will be used instead.
     * If no 'text'  option is specified the 'value' option will be used instead.
     *
     * @param 	array|KObjectConfig 	$config An optional array with configuration options
     * @return	string	Html
     * @see __call()
     */
    protected function _listbox($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'		  => '',
            'attribs'	  => array(),
            'model'		  => KStringInflector::pluralize($this->getIdentifier()->package),
            'deselect'    => true,
            'prompt'      => '- Select -',
            'unique'	  => true
        ))->append(array(
            'select2'         => false,
            'value'		      => $config->name,
            'selected'        => $config->{$config->name},
            'identifier'      => 'com://'.$this->getIdentifier()->application.'/'.$this->getIdentifier()->package.'.model.'.$config->model
        ))->append(array(
            'text'		      => $config->value,
        ))->append(array(
            'filter' 	      => array('sort' => $config->text),
        ));

        $html = '';

        if ($config->autocomplete) {
            $html .= $this->_autocomplete($config);
        }
        else
        {
            $options = array();

            $list = $this->getObject($config->identifier)->setState(KObjectConfig::unbox($config->filter))->getList();

            //Get the list of items
            $items = $list->getColumn($config->value);
            if ($config->unique) {
                $items = array_unique($items);
            }

            foreach ($items as $key => $value)
            {
                $item      = $list->find($key);
                $options[] = $this->option(array('text' => $item->{$config->text}, 'value' => $item->{$config->value}));
            }

            //Add the options to the config object
            $config->options = $options;

            $html .= $this->optionlist($config);
        }

        return $html;
    }

    /**
     * Renders a listbox with autocomplete behavior
     *
     * @see    ComKoowaTemplateHelperBehavior::_listbox
     *
     * @param  array|KObjectConfig    $config
     * @return string	The html output
     */
    protected function _autocomplete($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'attribs'  => array(),
            'validate' => true,
            'filter'   => array(),
            'element' => $config->attribs->id ? '#'.$config->attribs->id : 'input[name='.$config->name.']',
            'options' => array('multiple' => (bool) $config->attribs->multiple)
        ));

        if (!$config->url)
        {
            $identifier = $this->getIdentifier($config->identifier);
            $parts      = array(
                'option' => 'com_'.$identifier->package,
                'view'   => $identifier->name,
                'format' => 'json'
            );

            if ($config->filter) {
                $parts = array_merge($parts, KObjectConfig::unbox($config->filter));
            }

            $config->url = $this->getTemplate()->getView()->createRoute($parts, false, false);
        }

        $html = '';

        // TODO: Remove when select2 properly support AJAX multiple listboxes by sending choices
        // as an array (presumably for v4).
        if ($config->attribs->multiple)
        {
            $html .= '<script>
            jQuery(function($) {
                var el = $("' . $config->element . '");
                var form = el.closest("form");
                form.get(0).addEvent("submit", function() {
                    if (el.val()) {
                        var values = el.val().split(",");
                        $.each(values, function(idx, value) {
                            form.append(el.clone().val(value));
                        });
                        el.remove();
                    } else {
                        // If there is no value, then we remove the element anyways to mimic a real select box.
                        el.remove();
                    }
                });
            });</script>';
        }

        $html .= $this->getTemplate()->getHelper('behavior')->autocomplete($config);

        $config->attribs->name  = $config->name;

        if ($config->selected)
        {
            $config->attribs->value = json_encode(KObjectConfig::unbox($config->selected));
        }

        $attribs = $this->buildAttributes($config->attribs);

        $html .= "<input type=\"hidden\" {$attribs} />";

        return $html;
    }

    /**
     * Search the mixin method map and call the method or trigger an error
     *
     * This function check to see if the method exists in the mixing map if not it will call the 'listbox' function.
     * The method name will become the 'name' in the config array.
     *
     * This can be used to auto-magically create select filters based on the function name.
     *
     * @param  string   $method The function name
     * @param  array    $arguments The function arguments
     * @throws BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        if(!in_array($method, $this->getMethods()))
        {
            $config = $arguments[0];
            $config['name']  = KStringInflector::singularize(strtolower($method));

            return $this->_listbox($config);
        }

        return parent::__call($method, $arguments);
    }
}
