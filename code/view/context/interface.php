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
 * View Context Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View\Context
 */
interface ViewContextInterface extends CommandInterface
{
    /**
     * Get the view data
     *
     * @return array
     */
    public function getData();

    /**
     * Get the view content
     *
     * @return array
     */
    public function getContent();

    /**
     * Get the view parameters
     *
     * @return array
     */
    public function getParameters();
}