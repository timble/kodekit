<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
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