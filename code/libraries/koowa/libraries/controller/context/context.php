<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KControllerContext extends KCommand implements KControllerContextInterface
{
    /**
     * Get the request object
     *
     * @return KControllerRequestInterface
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Set the request object
     *
     * @param KControllerRequestInterface $request
     * @return $this
     */
    public function setRequest(KControllerRequestInterface $request)
    {
        $this->set('request', $request);
        return $this;
    }
}