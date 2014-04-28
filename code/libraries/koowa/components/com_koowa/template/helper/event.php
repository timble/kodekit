<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Event Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperEvent extends KTemplateHelperAbstract
{
    /**
     * Triggers an event and returns the results
     *
     * @param array $config
     * @return string
     */
    public function trigger($config = array())
    {
        // Can't put arguments through KObjectConfig as it loses referenced variables
        $arguments = isset($config['arguments']) ? $config['arguments'] : array();
        $config    = new KObjectConfig($config);

        if ($config->import_group) {
            JPluginHelper::importPlugin($config->import_group);
        }

        $result  = '';

        if ($config->event)
        {
            $results = JDispatcher::getInstance()->trigger($config->event, $arguments);
            $result  = trim(implode("\n", $results));
        }

        return $result;
    }
}