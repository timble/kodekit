<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Buffer FileSystem Stream
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Filesystem\Stream
 */
class ComKoowaFilesystemStreamBuffer extends KFilesystemStreamBuffer
{
    /**
     * Temporary directory
     */
    protected static $_temporary_directory;

    /**
     * Returns a directory path for temporary files
     *
     * Additionally checks for Joomla tmp folder if the system directory is not writable
     *
     * @throws RuntimeException If a temporary writable directory cannot be found
     * @return string Folder path
     */
    public function getTemporaryDirectory()
    {
        if (!self::$_temporary_directory)
        {
            $result     = false;
            $candidates = array(
                ini_get('upload_tmp_dir'),
                JFactory::getApplication()->getCfg('tmp_path'),
                JPATH_ROOT.'/tmp'
            );

            if (function_exists('sys_get_temp_dir')) {
                array_unshift($candidates, sys_get_temp_dir());
            }

            foreach ($candidates as $folder)
            {
                if ($folder && @is_dir($folder) && is_writable($folder))
                {
                    $result = rtrim($folder, '\\/');
                    break;
                }
            }

            if ($result === false) {
                throw new RuntimeException('Cannot find a writable temporary directory');
            }

            self::$_temporary_directory = $result;
        }

        return self::$_temporary_directory;
    }
}