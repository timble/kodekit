<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Menu bar Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
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
                $command->href = $this->getTemplate()->route($command->href);
            }

            JSubmenuHelper::addEntry($this->getObject('translator')->translate($command->label), $command->href, $command->active);
        }

        return '';
    }
}
