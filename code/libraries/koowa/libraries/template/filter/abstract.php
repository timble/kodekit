<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
abstract class KTemplateFilterAbstract extends KObject implements KTemplateFilterInterface
{
    /**
     * The filter priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Template object
     *
     * @var KTemplateInterface
     */
    protected $_template;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Set the template object
        $this->setTemplate($config->template);

        $this->_priority = $config->priority;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'template' => null,
            'priority' => self::PRIORITY_NORMAL
        ));

        parent::_initialize($config);
    }

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
     */
    public function translate($string, array $parameters = array())
    {
        return $this->getTemplate()->translate($string, $parameters);
    }

    /**
     * Escape a string
     *
     * By default the function uses htmlspecialchars to escape the string
     *
     * @param string $string String to to be escape
     * @return string Escaped string
     */
    public function escape($string)
    {
        return $this->getTemplate()->escape($string);
    }

    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Get the template object
     *
     * @return KTemplateInterface The template object
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set the template object
     *
     * @param   KTemplateInterface $template The template object
     * @return  KTemplateFilterInterface
     */
    public function setTemplate(KTemplateInterface $template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Method to extract key/value pairs out of a string with xml style attributes
     *
     * @param   string  $string String containing xml style attributes
     * @return  array   Key/Value pairs for the attributes
     */
    public function parseAttributes($string)
    {
        $result = array();

        if (!empty($string))
        {
            $attr = array();

            preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

            if (is_array($attr))
            {
                $numPairs = count($attr[1]);
                for ($i = 0; $i < $numPairs; $i++)
                {
                    $result[$attr[1][$i]] = $attr[2][$i];
                }
            }
        }

        return $result;
    }

    /**
     * Method to build a string with xml style attributes from  an array of key/value pairs
     *
     * @param   mixed   $array The array of Key/Value pairs for the attributes
     * @return  string  String containing xml style attributes
     */
    public function buildAttributes($array)
    {
        $output = array();

        if ($array instanceof KObjectConfig) {
            $array = KObjectConfig::unbox($array);
        }

        if (is_array($array))
        {
            foreach ($array as $key => $item)
            {
                if (is_array($item)) {
                    $item = implode(' ', $item);
                }

                if (is_bool($item))
                {
                    if ($item === false) continue;
                    $item = $key;
                }

                $output[] = $key . '="' . str_replace('"', '&quot;', $item) . '"';
            }
        }

        return implode(' ', $output);
    }
}
