<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

return array(
    'priority' => KObjectBootstrapper::PRIORITY_LOW,

    'aliases'  => array(
        'request'           => 'com:koowa.dispatcher.request',
        'translator'        => 'com:koowa.translator',
        'user'              => 'com:koowa.user',
        'filter.factory'    => 'com:koowa.filter.factory',
        'exception.handler' => 'com:koowa.exception.handler',
        'date'              => 'com:koowa.date',
        'event.publisher'   => 'com:koowa.event.publisher',
        'user.provider'     => 'com:koowa.user.provider',

        'lib:database.adapter.mysqli'  => 'com:koowa.database.adapter.mysqli',
        'lib:dispatcher.router.route'  => 'com:koowa.dispatcher.router.route',
        'lib:filesystem.stream.buffer' => 'com:koowa.filesystem.stream.buffer'
    ),
);