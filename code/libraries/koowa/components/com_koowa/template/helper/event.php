<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Event Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperEvent extends KTemplateHelperAbstract
{
    /**
     * Triggers an event and returns the results
     *
     * @param array $config
     * @throws InvalidArgumentException
     * @return string
     */
    public function trigger($config = array())
    {
        // Can't put arguments through KObjectConfig as it loses referenced variables
        $attributes = isset($config['attributes']) ? $config['attributes'] : array();
        $config     = new KObjectConfig($config);
        $config->append(array(
            'name'         => null,
            'import_group' => null
        ));

        if (empty($config->name)) {
            throw new InvalidArgumentException('Event name is required');
        }

        if ($config->import_group) {
            JPluginHelper::importPlugin($config->import_group);
        }

        $results = JDispatcher::getInstance()->trigger($config->name, $attributes);
        $result  = trim(implode("\n", $results));

        return $result;
    }
}