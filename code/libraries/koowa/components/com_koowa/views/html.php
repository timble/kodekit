<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Html View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa\View
 */
class ComKoowaViewHtml extends KViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'template_filters' => array('version')
        ));

        parent::_initialize($config);
    }
}
