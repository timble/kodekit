<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Html View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Module\Koowa\View
 */
class ModKoowaHtml extends KViewHtml
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'template_filters'   => array('version', 'chrome', 'style', 'link', 'meta', 'script', 'title'),
            'data'             => array(
                'styles' => array()
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Get the name
     *
     * @return 	string 	The name of the object
     */
    public function getName()
    {
        return $this->getIdentifier()->package;
    }

    /**
     * Load the controller translations
     *
     * @param KViewContext $context
     * @return void
     */
    protected function _loadTranslations(KViewContext $context)
    {
        if(isset($this->module->module))
        {
            $package = $this->getIdentifier()->package;
            $domain  = $this->getIdentifier()->domain;

            if($domain) {
                $identifier = 'mod://'.$domain.'/'.$package;
            } else {
                $identifier = 'mod:'.$package;
            }

            $this->getObject('translator')->load($identifier);
        }
    }

    /**
     * Return the views output
     *
     * @param KViewContext  $context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
        if(empty($this->module->content))
        {
            $format = $this->getFormat(); //format cannot be changed through context
            $layout = $context->layout;

            if (is_string($layout) && strpos($layout, '.') === false)
            {
                $identifier = $this->getIdentifier()->toArray();
                $identifier['name'] = $layout;

                $layout = (string) $this->getIdentifier($identifier);
            }

            //Unpack the data (first level only)
            $data = array();
            foreach($context->data as $key => $value) {
                $data[$key] = $value;
            }

            $this->_content = $this->getTemplate()
                ->loadFile($layout.'.'.$format)
                ->setParameters($context->parameters)
                ->render($data);
        }
        else
        {
            $this->_content = $this->getTemplate()
                ->loadString($this->module->content)
                ->setParameters($context->parameters)
                ->filter();
        }

        return $this->_content;
    }

    /**
     * Accepts Joomla style module layout formats such as _:default.html
     *
     * {@inheritdoc}
     */
    public function setLayout($layout)
    {
        if (strpos($layout, ':'))
        {
            $layout = explode(':', $layout);
            $layout = str_replace('.html', '', array_pop($layout));
        }

        return parent::setLayout($layout);
    }

    /**
     * Set a view properties
     *
     * @param   string  $property The property name.
     * @param   mixed   $value    The property value.
     */
    public function set($property, $value)
    {
        if($property == 'module')
        {
            $value = clone $value;

            if(is_string($value->params)) {
                $value->params = $this->getObject('object.config.factory')->fromString('json', $value->params);
            }
        }

        parent::set($property, $value);
    }
}
