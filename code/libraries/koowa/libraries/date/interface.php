<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Date Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Date
 */
interface KDateInterface extends KObjectHandlable
{
    /**
     * Returns the date formatted according to given format.
     *
     * @param  string $format The format to use
     * @return string
     */
    public function format($format);

    /**
     * Returns human readable date.
     *
     * @param  string $period The smallest period to use. Default is 'second'.
     * @return string Formatted date.
     */
    public function humanize($period = 'second');
}