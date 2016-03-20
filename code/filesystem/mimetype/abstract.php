<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Abstract FileSystem Mimetype Resolver
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Kodekit\Library\Filesystem\Mimetype
 */
abstract class FilesystemMimetypeAbstract extends Object implements FilesystemMimetypeInterface
{
    /**
     * Check if the resolver is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public static function isSupported()
    {
        return true;
    }
}