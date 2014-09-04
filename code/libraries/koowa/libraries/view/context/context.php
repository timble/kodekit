<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * View Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View\Context
 */
class KViewContext extends KCommand implements KViewContextInterface
{
    /**
     * The view data
     *
     * @var KObjectArray
     */
    private $__data;

    /**
     * Set the view data
     *
     * Box the view data as an object array to prevent it from being unboxed.
     *
     * @param array $data
     * @return KViewContext
     */
    public function setData($data)
    {
        $this->__data = new KObjectArray(new KObjectConfig(array('data' => $data)));
        return $this;
    }

    /**
     * Get the view data
     *
     * @return KObjectArray
     */
    public function getData()
    {
        return $this->__data;
    }
}