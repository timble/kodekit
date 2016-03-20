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
 * View Templatable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View
 */
interface ViewTemplatable
{
    /**
     * Get the layout
     *
     * @return string The layout name
     */
    public function getLayout();

    /**
     * Get the template object attached to the view
     *
     *  @throws	\UnexpectedValueException	If the template doesn't implement the TemplateInterface
     * @return  TemplateInterface
     */
    public function getTemplate();
}