<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Template Template Filter
 *
 * Filter for the @template alias. To load templates inline
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterTemplate extends KTemplateFilterAbstract implements KTemplateFilterRead
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Replace template alias with loadFile functions.
     *
     * This function only replaces relative identifiers to a full path based on the path of the template.
     *
     * @param string $text
     * @return KTemplateFilterAlias
     */
    public function read(&$text)
    {
        if(preg_match_all('#@template\(\'(.*)\'#siU', $text, $matches))
		{
			foreach($matches[1] as $key => $match)
			{
			    if(is_string($match) && strpos($match, '.') === false )
		        {
		            $path =  dirname($this->getTemplate()->getPath()).DIRECTORY_SEPARATOR.$match.'.php';
		            $text = str_replace($matches[0][$key], '$this->loadFile('."'".$path."'", $text);
		        }
			}
		}

        return $this;
    }
}
