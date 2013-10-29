<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
abstract class KTemplateLocatorAbstract extends KObject implements KTemplateLocatorInterface
{
    /**
     * The type
     *
     * @var string
     */
    protected $_type = '';

    /**
     * Template object
     *
     * @var	KTemplateInterface
     */
    protected $_template;

    /**
     * Constructor
     *
     * @param KObjectConfig $config	An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Set the template object
        $this->setTemplate($config->template);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'template' => null,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set the template object
     *
     * @param  KTemplateInterface $template	The template object
     * @return $this
     */
    public function setTemplate(KTemplateInterface $template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Get the template object
     *
     * @return  object	The template object
     */
    public function getTemplate()
    {
        return $this->_template;
    }
}