<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Toolbar Template Filter
 *
 * Filter will parse <ktml:toolbar type="[type]'> tags and replace them with the actual toolbar html by rendering
 * the toolbar helper for the specific toolbar type.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComKoowaTemplateFilterToolbar extends KTemplateFilterAbstract
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
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setToolbars(KObjectConfig::unbox($config->toolbars));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
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
     * @return $this
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
     * Returns the specified toolbar instance
     *
     * @param  string $type Toolbar type
     * @return KControllerToolbarInterface|null
     */
    public function getToolbar($type = 'actionbar')
    {
        return isset($this->_toolbars[$type]) ? $this->_toolbars[$type] : null;
    }

    /**
     * Sets the menu bar instance
     *
     * @param KControllerToolbarInterface $toolbar
     * @return ComKoowaTemplateFilterToolbar
     */
    public function setToolbar(KControllerToolbarInterface $toolbar)
    {
        $this->_toolbars[$toolbar->getType()] = $toolbar;
        return $this;
    }

    /**
     * Replace/push the toolbars
     *
     * @param string $text Block of text to parse
     * @return ComKoowaTemplateFilterToolbar
     */
    public function filter(&$text)
    {
        $matches = array();

        if(preg_match_all('#<ktml:toolbar([^>]*)>#siU', $text, $matches))
        {
            foreach($matches[0] as $key => $match)
            {
                $attributes = $this->parseAttributes($matches[1][$key]);

                //Create attributes array
                $config = new KObjectConfig($attributes);
                $config->append(array(
                    'type'  => 'actionbar',
                ));

                $html = '';
                if($toolbar = $this->getToolbar($config->type))
                {
                    $config->toolbar = $toolbar; //set the toolbar in the config

                    $html = $this->getTemplate()
                                 ->createHelper($config->type)
                                 ->render($config);
                }

                //Remove placeholder
                $text = str_replace($match, $html, $text);
            }
        }

        return $this;
    }
}