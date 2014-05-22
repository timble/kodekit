<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Menu bar Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperMenubar extends KTemplateHelperAbstract
{
 	/**
     * Render the menu bar
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
        	'toolbar' => null
        ));

        foreach ($config->toolbar->getCommands() as $command)
        {
            if(!empty($command->href)) {
                $command->href = $this->getTemplate()->getView()->getRoute($command->href);
            }

            JSubmenuHelper::addEntry($this->translate($command->label), $command->href, $command->active);
        }

		return '';
    }
}
