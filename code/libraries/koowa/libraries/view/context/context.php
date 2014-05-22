<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * View Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
class KViewContext extends KCommand implements KViewContextInterface
{
    /**
     * Set the view data
     *
     * @param array $data
     * @return KViewContext
     */
    public function setData($data)
    {
        $this->set('data', $data);
        return $this;
    }

    /**
     * Get the view data
     *
     * @return array
     */
    public function getData()
    {
        return $this->get('data');
    }
}