<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Guesses the mime type using the PECL extension FileInfo.
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Filesystem\Mimetype\Guesser
 */
class KFilesystemMimetypeGuesserFileinfo extends KObject implements KFilesystemMimetypeGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'magic_file' => null
        ));

        parent::_initialize($config);
    }

    /**
     * Returns whether this guesser is supported on the current OS/PHP setup.
     *
     * @return bool
     */
    public static function isSupported()
    {
        return function_exists('finfo_open');
    }
    /**
     * {@inheritdoc}
     */
    public function guess($path)
    {
        if (!is_file($path)) {
            throw new \RuntimeException('File not found at '.$path);
        }

        if (!is_readable($path)) {
            throw new \RuntimeException('File not readable at '.$path);
        }

        $mimetype = null;

        if (static::isSupported())
        {
            $finfo = new \finfo(FILEINFO_MIME_TYPE, $this->getConfig()->magic_file);

            if ($finfo) {
                $mimetype = $finfo->file($path);
            }
        }

        return $mimetype;
    }
}