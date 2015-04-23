<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Template View Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View\Context
 */
class KViewContextTemplate extends KViewContext
{
    /**
     * Set the view layout
     *
     * @param string $layout
     * @return KViewContextTemplate
     */
    public function setLayout($layout)
    {
        return KObjectConfig::set('layout', $layout);
    }

    /**
     * Get the view layout
     *
     * @return array
     */
    public function getLayout()
    {
        return KObjectConfig::get('layout');
    }
}