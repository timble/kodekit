<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Debug Template Helper
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperDebug extends KTemplateHelperDebug
{
    /**
     * Removes Joomla root from a filename replacing them with the plain text equivalents.
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function path($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'root'  => JPATH_ROOT,
        ));

        return parent::path($config);
    }
}