<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Find the mime type of a file using the file extension. Lookups are performed using a provided JSON lookup file.
 *
 * JSON should be structured as a map of extension to mimetype
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Filesystem\Mimetype
 */
class KFilesystemMimetypeExtension extends KFilesystemMimetypeAbstract
{
    /**
     * The mimetypes
     *
     * @var array
     */
    protected $_mimetypes;

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'file' => __DIR__.'/mimetypes.json'
        ));

        parent::_initialize($config);
    }

    /**
     * Find the mime type of the file with the given path.
     *
     * @param string $path The path to the file
     * @return string The mime type or NULL, if none could be guessed
     */
    public function fromPath($path)
    {
        $mimetype = null;

        if (static::isSupported())
        {
            if (!is_file($path)) {
                throw new \RuntimeException('File not found at '.$path);
            }

            if (!is_readable($path)) {
                throw new \RuntimeException('File not readable at '.$path);
            }

            //Get the mimetypes from the JSON file
            if(!isset($this->_mimetypes))
            {
                $file = $this->getConfig()->file;

                if (is_readable($file)) {
                    $this->_mimetypes = json_decode(file_get_contents($file), true);
                }
            }

            //Find the mimetype from the path extension
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if (isset($this->_mimetypes[$extension])) {
                $mimetype = $this->_mimetypes[$extension];
            }
        }

        return $mimetype;
    }
}