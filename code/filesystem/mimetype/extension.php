<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Guesses the mime type using the list in the provided JSON file
 *
 * JSON should be structured as a map of extension to mimetype
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Filesystem\Mimetype
 */
class KFilesystemMimetypeExtension extends KObject implements KFilesystemMimetypeInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'file' => __DIR__.'/mimetypes.json'
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function isSupported()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function find($path)
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
            $file = $this->getConfig()->file;

            if (is_readable($file))
            {
                $mimetypes = json_decode(file_get_contents($file), true);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                if (isset($mimetypes[$extension])) {
                    $mimetype = $mimetypes[$extension];
                }
            }
        }

        return $mimetype;
    }
}