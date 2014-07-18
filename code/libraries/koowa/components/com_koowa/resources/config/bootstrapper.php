<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

return array(
    'priority' => KObjectBootstrapper::PRIORITY_LOW,

    'aliases'  => array(
        'request'                       => 'com:koowa.dispatcher.request',
        'lib:database.adapter.mysqli'   => 'com:koowa.database.adapter.mysqli',
        'translator'                    => 'com:koowa.translator',
        'user'                          => 'com:koowa.user',
        'filter.factory'                => 'com:koowa.filter.factory',
        'exception.handler'             => 'com:koowa.exception.handler',
        'date'                          => 'com:koowa.date',
        'event.publisher'               => 'com:koowa.event.publisher',
        'user.provider'                 => 'com:koowa.user.provider'
    ),
);