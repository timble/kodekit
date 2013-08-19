<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Instantiatable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Service
 */
interface KObjectInstantiatable
{
    /**
     * Get the object identifier
     *
     * @param   KObjectConfigInterface $config        Configuration options
     * @param 	KObjectManagerInterface $manager	A KObjectManagerInterface object
     * @return  object
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager);
}
