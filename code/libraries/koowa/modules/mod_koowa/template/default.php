<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Default Template
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Module\Koowa
 */
final class ModKoowaTemplateDefault extends ComKoowaTemplateAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'locators' => array('mod' => 'mod:koowa.template.locator.module')
        ));

        parent::_initialize($config);
    }
}
