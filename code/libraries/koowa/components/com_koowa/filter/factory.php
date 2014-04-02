<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Filter Factory
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class ComKoowaFilterFactory extends KFilterFactory
{
    /**
     * Changes the default prefix for filters to com:koowa
     *
     * {@inheritdoc}
     */
    protected function _createFilter($filter, $config)
    {
        if(is_string($filter) && strpos($filter, '.') === false ) {
            $filter = 'com:koowa.filter.'.trim($filter);
        }

        return parent::_createFilter($filter, $config);
    }
}