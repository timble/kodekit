<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Recursive Controller Toolbar Iterator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar
 */
class KControllerToolbarIteratorRecursive extends \RecursiveIteratorIterator
{
    /**
     * Constructor
     *
     * @param KControllerToolbarInterface $toolbar
     * @param integer $max_level The maximum allowed level. 0 is used for any level
     * @return KControllerToolbarIteratorRecursive
     */
    public function __construct(KControllerToolbarInterface $toolbar, $max_level = 0)
    {
        parent::__construct(static::_createInnerIterator($toolbar), \RecursiveIteratorIterator::SELF_FIRST);

        //Set the max iteration level
        if(isset($max_level)) {
            $this->setMaxLevel($max_level);
        }
    }

    /**
     * Get children of the current command
     *
     * @return \RecursiveIterator
     */
    public function callGetChildren()
    {
        return static::_createInnerIterator($this->current());
    }

    /*
     * Called for each element to test whether it has children.
     *
     * @return bool TRUE if the element has children, otherwise FALSE
     */
    public function callHasChildren()
    {
        return (bool) count($this->current());
    }

    /**
     * Set the maximum iterator level
     *
     * @param int $max
     * @return KControllerToolbarIteratorRecursive
     */
    public function setMaxLevel($max = 0)
    {
        //Set the max depth for the iterator
        $this->setMaxDepth((int) $max - 1);
        return $this;
    }

    /**
     * Get the current iteration level
     *
     * @return int
     */
    public function getLevel()
    {
        return (int) $this->getDepth() + 1;
    }

    /**
     * Create a recursive iterator from a toolbar
     *
     * @param KControllerToolbarInterface $toolbar
     * @return \RecursiveIterator
     */
    protected static function _createInnerIterator(KControllerToolbarInterface $toolbar)
    {
        $iterator = new \RecursiveArrayIterator($toolbar->getIterator());
        $iterator = new \RecursiveCachingIterator($iterator, \CachingIterator::TOSTRING_USE_KEY);

        return $iterator;
    }
}