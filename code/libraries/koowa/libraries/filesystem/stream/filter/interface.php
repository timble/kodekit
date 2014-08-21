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
interface KFilesystemStreamFilterInterface
{
    /**
     * Called when applying the filter
     *
     * @param resource $in  Resource pointing to a bucket brigade which contains one or more bucket objects containing
     *                      data to be filtered
     * @param resource $out Resource pointing to a second bucket brigade into which your modified buckets should be
     *                      placed.
     * @param integer $consumed Consumed, which must always be declared by reference, should be incremented by the length
     *                          of the data which your filter reads in and alters. In most cases this means you will
     *                          increment consumed by $bucket->datalen for each $bucket.
     * @param bool $closing If the stream is in the process of closing (and therefore this is the last pass through the
     *                      filterchain), the closing parameter will be set to TRUE.
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing);

    /**
     * Get the filter name used to register the stream with
     *
     * @return string The filter name
     */
    public static function getName();

    /**
     * Called the filter is created
     *
     * @return bool Return FALSE on failure, or TRUE on success.
     */
    public function onCreate();

    /**
     * Called when closing the filter
     *
     * This method is called upon filter shutdown (typically, this is also during stream shutdown), and is executed after
     * the flush method is called. If any resources were allocated or initialized during onCreate() this would be the time
     * to destroy or dispose of them.
     *
     * @return void
     */
    public function onClose();


}