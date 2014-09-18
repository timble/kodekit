<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Chrome Template Filter
 *
 * This filter allows to apply module chrome to a template
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComKoowaTemplateFilterChrome extends KTemplateFilterAbstract
{
    /**
     * The module title
     *
     * If set this will be passed to the module chrome rendered. If the renderer support rendering of a title it
     * will be displayed.
     *
     * @var string
     */
    protected $_title;

    /**
     * The module class
     *
     * @var string
     */
    protected $_class;

    /**
     * The module styles
     *
     * @var array
     */
    protected $_styles;

    /**
     * The module attribs
     *
     * @var array
     */
    protected $_attribs;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct( KObjectConfig $config = null)
    {
        parent::__construct($config);

        $this->_title   = $config->title;
        $this->_class   = $config->class;
        $this->_styles  = KObjectConfig::unbox($config->styles);
        $this->_attribs = KObjectConfig::unbox($config->attribs);
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
            'priority' => self::PRIORITY_LOW,
            'title'    => '',
            'class'    => '',
            'styles'   => array(),
            'attribs'  => array(
                'name'  => $this->getIdentifier()->package . '_' . $this->getIdentifier()->name,
                'first' => null,
                'last'  => null,
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Apply module chrome to the template output
     *
     * @param string $text Block of text to parse
     * @return ComKoowaTemplateFilterChrome
     */
    public function filter(&$text)
    {
        $name = $this->getIdentifier()->package . '_' . $this->getIdentifier()->name;

        //Create a module object
        $module   	       = new KObject(new KObjectConfig());
        $module->id        = uniqid();
        $module->module    = 'mod_'.$name;
        $module->content   = $text;
        $module->position  = $name;
        $module->params    = 'moduleclass_sfx='.$this->_class;
        $module->showtitle = (bool) $this->_title;
        $module->title     = $this->_title;
        $module->user      = 0;

        $text = $this->getObject('mod://admin/koowa.html')->module($module)->attribs($this->_attribs)->styles($this->_styles)->render();

        return $this;
    }
}
