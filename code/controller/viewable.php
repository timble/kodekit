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
 * Controller Viewable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller
 */
interface ControllerViewable
{
    /**
     * Get the controller view
     *
     * @throws  \UnexpectedValueException    If the view doesn't implement the ViewInterface
     * @return  ViewInterface
     */
    public function getView();

    /**
     * Get the supported formats
     *
     * @return array
     */
    public function getFormats();
}