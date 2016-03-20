<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Template View Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View\Context
 */
class ViewContextTemplate extends ViewContext
{
    /**
     * Set the view layout
     *
     * @param string $layout
     * @return ViewContextTemplate
     */
    public function setLayout($layout)
    {
        return ObjectConfig::set('layout', $layout);
    }

    /**
     * Get the view layout
     *
     * @return array
     */
    public function getLayout()
    {
        return ObjectConfig::get('layout');
    }
}