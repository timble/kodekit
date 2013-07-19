<?php
/**
 * @version		$Id$
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Helper Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 */
abstract class KTemplateHelperAbstract extends KObject implements KTemplateHelperInterface
{
	/**
	 * Template object
	 *
	 * @var	object
	 */
    protected $_template;

	/**
	 * Constructor
	 *
	 * Prevent creating instances of this class by making the contructor private
	 *
	 * @param   KConfig $config Configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->setTemplate($config->template);
	}

    /**
     * Gets the template object
     *
     * @return  KTemplateInterface	The template object
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Sets the template object
     *
     * @param string|KTemplateInterface $template A template object or identifier
     * @return $this
     */
    public function setTemplate($template)
    {
        if(!$template instanceof KTemplateAbstract)
        {
            if(empty($template) || (is_string($template) && strpos($template, '.') === false) )
            {
                $identifier			= clone $this->getIdentifier();
                $identifier->path	= array('template');
                $identifier->name	= $template ? $template : 'default';
            } else $identifier = $this->getIdentifier($template);
	
            $template = $this->getService($identifier);
        }
    
        $this->_template = $template;
    
        return $this;
    }

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     *
     * @return string Translated string
     */
    public function translate($string, array $parameters = array())
    {
        return $this->getTemplate()->translate($string, $parameters);
    }
}