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
 * Toolbar Template Helper
 *
 * Extended by each specific toolbar renderer
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
abstract class TemplateHelperToolbar extends TemplateHelperAbstract
{
    /**
     * Returns the type of toolbar this helper can render
     *
     * @return string
     */
    public function getToolbarType()
    {
        return $this->getIdentifier()->getName();
    }
}