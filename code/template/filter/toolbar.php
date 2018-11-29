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
 * Toolbar Template Filter
 *
 * Filter will parse <ktml:toolbar type="[type]'> tags and replace them with the actual toolbar html by rendering
 * the toolbar helper for the specific toolbar type.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
class TemplateFilterToolbar extends TemplateFilterAbstract
{
    /**
     * Toolbars to render such as actionbar, menubar, ...
     *
     * @var array
     */
    protected $_toolbars;

    /**
     * Constructor
     *
     * @param  ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->setToolbars(ObjectConfig::unbox($config->toolbars));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'toolbars' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Get the list of toolbars to be rendered
     *
     * @return array
     */
    public function getToolbars()
    {
        return $this->_toolbars;
    }

    /**
     * Set the toolbars to render
     *
     * @param array $toolbars
     * @return TemplateFilterToolbar
     */
    public function setToolbars(array $toolbars)
    {
        $this->_toolbars = array();
        foreach($toolbars as $toolbar) {
            $this->setToolbar($toolbar);
        }

        return $this;
    }

    /**
     * Get a toolbar by type
     *
     * @param  string $type Toolbar type
     * @return ControllerToolbarInterface
     */
    public function getToolbar($type = 'actionbar')
    {
        return isset($this->_toolbars[$type]) ? $this->_toolbars[$type] : null;
    }

    /**
     * Sets a toolbar
     *
     * @param  ControllerToolbarInterface $toolbar
     * @return TemplateFilterToolbar
     */
    public function setToolbar(ControllerToolbarInterface $toolbar)
    {
        $this->_toolbars[$toolbar->getType()] = $toolbar;
        return $this;
    }

    /**
     * Replace/push the toolbars
     *
     * @param string $text  The text to parse
     * @param TemplateInterface $template A template object
     * @return void
     */
    public function filter(&$text, TemplateInterface $template)
    {
        $matches = array();

        if(preg_match_all('#<ktml:toolbar([^>]*)>#siU', $text, $matches))
        {
            foreach($matches[0] as $key => $match)
            {
                $attributes = $this->parseAttributes($matches[1][$key]);

                //Create attributes array
                $config = new ObjectConfig($attributes);
                $config->append(array(
                    'type'  => 'actionbar',
                ));

                if($this->getIdentifier()->type != 'lib')
                {
                    if ($this->getIdentifier()->domain) {
                        $identifier = 'com://'.$this->getIdentifier()->domain.'/'.$this->getIdentifier()->package.'.template.helper.'.$config->type;
                    } else {
                        $identifier = 'com:'.$this->getIdentifier()->package.'.template.helper.'.$config->type;
                    }
                } else {
                    $identifier = 'lib:template.helper.'.$config->type;
                }

                $helper = $this->getObject('template.helper.factory')->createHelper($identifier);

                $html = '';
                if($toolbar = $this->getToolbar($helper->getToolbarType()))
                {
                    $config->toolbar = $toolbar; //set the toolbar in the config
                    
                    $html = $helper->render($config);
                }

                //Remove placeholder
                $text = str_replace($match, $html, $text);
            }
        }
    }
}