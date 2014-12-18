<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * FileSystem Stream Filter Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filesystem\Stream\Filter
 */
abstract class KFilesystemStreamFilterAbstract extends php_user_filter implements KFilesystemStreamFilterInterface
{
    /**
     * The filter name
     *
     * @var string
     */
    protected static $_name = '';

    /**
     * The filter name
     *
     * String containing the name the filter was instantiated with. Filters may be registered under multiple names or
     * under wildcards. Use this property to determine which name was used.
     *
     * @var string
     * @see php_user_filter
     */
    public $filtername;

    /**
     * The stream being filtered
     *
     * The stream resource being filtered. Maybe available only during filter() calls when the closing parameter is
     * set to FALSE.
     *
     * @var resource
     * @see php_user_filter
     */
    public $stream;

    /**
     * The filter params
     *
     * The contents of the params parameter passed to stream_filter_append() or stream_filter_prepend().
     *
     * @var array
     * @see php_user_filter
     */
    public $params;

    /**
     * Get the stream name used to register the stream with
     *
     * @return string The stream name
     */
    public static function getName()
    {
        return static::$_name;
    }

    /**
     * Called the filter is created
     *
     * @return bool Return FALSE on failure, or TRUE on success.
     */
    public function onCreate()
    {
        //do nothing
        return true;
    }

    /**
     * Called when closing the filter
     *
     * This method is called upon filter shutdown (typically, this is also during stream shutdown), and is executed after
     * the flush method is called. If any resources were allocated or initialized during onCreate() this would be the time
     * to destroy or dispose of them.
     *
     * @return void
     */
    public function onClose()
    {
        //do nothing
    }
}