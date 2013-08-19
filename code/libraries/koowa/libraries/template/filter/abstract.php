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
     * The behavior priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Template object
     *
     * @var object
     */
    protected $_template;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_priority = $config->priority;

        if ($config->template) {
            $this->setTemplate($config->template);
        }
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
            'priority' => KCommand::PRIORITY_NORMAL,
            'template' => null
        ));

        parent::_initialize($config);
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
     * @return  object	The template object
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set the template object
     *
     * @param   KTemplateInterface $template The template object
     * @return  $this
     */
    public function setTemplate($template)
    {
        $this->_template = $template;

        return $this;
    }

    /**
     * Command handler
     *
     * @param   string          $name    The command name
     * @param   KCommandContext $context The command context
     * @return  boolean     Always returns TRUE
     */
    final public function execute($name, KCommandContext $context)
    {
        //Set the template
        $this->_template = $context->caller;

        //Set the data
        $data = $context->data;

        if(($name & KTemplateFilter::MODE_READ) && $this instanceof KTemplateFilterRead) {
            $this->read($data);
        }

        if(($name & KTemplateFilter::MODE_WRITE) && $this instanceof KTemplateFilterWrite) {
            $this->write($data);
        }

        //Get the data
        $context->data = $data;

        //Reset the template
        //$this->_template = null;

        //@TODO : Allows filters to return false and halt the filter chain
        return true;
    }

    /**
     * Method to extract key/value pairs out of a string with xml style attributes
     *
     * @param   string  String containing xml style attributes
     * @return  array   Key/Value pairs for the attributes
     */
    public function parseAttributes( $string )
    {
        $result = array();

        if(!empty($string))
        {
            $attr   = array();

            preg_match_all( '/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr );

            if (is_array($attr))
            {
                $numPairs = count($attr[1]);
                for($i = 0; $i < $numPairs; $i++ ) {
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

                $output[] = $key . '="' . str_replace('"', '&quot;', $item) . '"';
            }
        }

        return implode(' ', $output);
    }
}
