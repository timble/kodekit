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

        $list = $this->getObject($config->identifier)->set($config->filter)->getList();

        //Get the list of items
        $items = $list->getColumn($config->value);
        if($config->unique) {
            $items = array_unique($items);
        }

        //Compose the options array
        $options   = array();
        if($config->deselect) {
            $options[] = $this->option(array('text' => $this->translate($config->prompt)));
        }

        foreach($items as $key => $value)
        {
            $item      = $list->find($key);
            $options[] = $this->option(array('text' => $item->{$config->text}, 'value' => $item->{$config->value}));
        }

        //Add the options to the config object
        $config->options = $options;

        $html = '';

        if ($config->autocomplete) {
            $html .= $this->_autocomplete($config);
        }
        elseif ($config->select2) {
            $html .= $this->_listboxSelect2($config);
        }

        $html .= $this->optionlist($config);

        return $html;
    }

    /**
     * Enhances a select box using Select2
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    protected function _listboxSelect2($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'attribs' => array()
        ))->append(array(
            'select2_options' => array(
                'element' => $config->attribs->id ? '#'.$config->attribs->id : 'select[name='.$config->name.']',
                'options' => array()
            )
        ));

        $html = '';

        if ($config->deselect)
        {
            if (!$config->attribs->multiple && !$config->select2_options->options->multiple)
            {
                // select2 needs the first option empty for placeholders to work on single select boxes
                $config->options[0]->text = '';
            }
            else
            {
                // get rid of the deselect option as we set the placeholder property below
                $options =& $config->options;
                unset($options[0]);
            }

            $config->select2_options->append(array('options' => array(
                'placeholder' => $config->prompt,
                'allowClear'  => true
            )));
        }

        $html .= $this->getTemplate()->getHelper('behavior')->select2($config->select2_options);

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
            'name'                 => '',
            'attribs'              => array(),
            'model'                => KStringInflector::pluralize($this->getIdentifier()->package),
            'validate'             => true,
            'deselect'             => false,
            'prompt'               => false
        ))->append(array(
            'value'                => $config->name,
            'selected'             => $config->{$config->name},
            'identifier'           => 'com://'.$this->getIdentifier()->application.'/'.$this->getIdentifier()->package.'.model.'.KStringInflector::pluralize($config->model)
        ))->append(array(
            'text'                 => $config->value,
        ))->append(array(
            'filter'               => array(),
            'autocomplete_options' => array(
                'element' => 'select[name='.$config->name.']',
                'url'     => JRoute::_('index.php?option=com_'.$this->getIdentifier($config->identifier)->package.'&view='.$config->model.'&format=json', false),
                'options' => array()
            )
        ));

        //For the autocomplete behavior
        $options = new KObjectConfig($config->autocomplete_options);
        $shortcuts = array('name', 'model', 'validate', 'deselect', 'prompt', 'value', 'selected', 'text', 'filter');
        foreach($shortcuts as $key) {
            $options->append(array($key => $config->{$key}));
        }

        $html = $this->getTemplate()->getHelper('behavior')->autocomplete($options);

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
