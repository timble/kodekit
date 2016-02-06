<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Database table for Joomla templates
 *
 * @author  Dave Li <https://github.com/daveli>
 * @package Koowa\Component\Koowa\Database\Table
 */
class ComKoowaDatabaseTableTemplates extends KDatabaseTableAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'name' => 'template_styles'
        ));

        parent::_initialize($config);
    }
}
