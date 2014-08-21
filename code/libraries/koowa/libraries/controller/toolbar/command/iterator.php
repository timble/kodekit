<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Toolbar Command Iterator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar\Command
 */
class KControllerToolbarCommandIterator extends RecursiveIteratorIterator
{
    /**
     * Constructor
     *
     * @param KControllerToolbarInterface $toolbar
     * @param int                         $mode
     * @param int                         $flags
     */
    public function __construct(KControllerToolbarInterface $toolbar, $mode = RecursiveIteratorIterator::SELF_FIRST, $flags = 0)
    {
        parent::__construct($toolbar, $mode, $flags);
    }

    /**
     * Returns an iterator for element children
     *
     * @return RecursiveIterator
     */
    public function callGetChildren()
    {
        return $this->current()->getIterator();
    }

    /**
     * Called for each element to test whether it has children.
     *
     * @return bool True if element has children
     */
    public function callHasChildren()
    {
        return (boolean) count($this->current());
    }
}