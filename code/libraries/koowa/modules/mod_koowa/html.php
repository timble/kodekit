<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Html View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Module\Koowa
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
        	'template_filters' => array('chrome', 'style', 'link', 'meta', 'script', 'title'),
            'data'			   => array(
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
     * Return the views output
     *
     * @param KViewContext	$context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
		//Load the language files.
		if(isset($this->module->module)) {
            $this->getObject('translator')->loadTranslations($this->getIdentifier());
		}

        if(empty($this->module->content))
        {
            $layout = $this->getLayout();
            $format = $this->getFormat();

            if (is_string($layout) && strpos($layout, '.') === false)
            {
                $identifier = clone $this->getIdentifier();
                $identifier->name = $layout;

                $layout = (string) $identifier;
            }

            $this->getTemplate()->load($layout.'.'.$format);
		}
		else $this->getTemplate()->setContent($this->module->content);

        $this->_content = (string) $this->getTemplate()
            ->compile()
            ->evaluate($this->_data)
            ->render();

        return $this->_content;
    }

    /**
     * Set a view properties
     *
     * @param   string  $property The property name.
     * @param   mixed   $value    The property value.
     */
    public function __set($property, $value)
    {
        if($property == 'module')
        {
            $value = clone $value;

            if(is_string($value->params)) {
                $value->params = $this->_parseParams($value->params);
            }
        }

        parent::__set($property, $value);
    }

	/**
     * Method to extract key/value pairs out of a string
     *
     * @param   string  String containing the parameters
     * @return  array   Key/Value pairs for the attributes
     */
    protected function _parseParams( $string )
    {
        $params = new KObjectConfig((array) json_decode($string));
        return $params;
    }
}
