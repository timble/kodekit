<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Guesses the mime type using the file system command.
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Filesystem\Mimetype\Guesser
 */
class KFilesystemMimetypeGuesserBinary extends KObject implements KFilesystemMimetypeGuesserInterface
{
    /**
     * Initializer
     *
     * command property must contain a "%s" string that will be replaced
     * with the file name to guess.
     *
     * @param KObjectConfig $config
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'command' => 'file -b --mime %s 2>/dev/null'
        ));

        parent::_initialize($config);
    }

    /**
     * Returns whether this guesser is supported on the current OS.
     *
     * @return bool
     */
    public static function isSupported()
    {
        return '\\' !== DIRECTORY_SEPARATOR && function_exists('passthru') && function_exists('escapeshellarg');
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
            ob_start();

            // need to use --mime instead of -i. see #6641
            passthru(sprintf($this->getConfig()->command, escapeshellarg($path)), $return);

            if ($return == 0)
            {
                $type = trim(ob_get_clean());

                // is it a type or an error message?
                if (preg_match('#^([a-z0-9\-]+/[a-z0-9\-\.]+)#i', $type, $match)) {
                    $mimetype = $match[1];
                }
            }

            ob_end_clean();
        }

        return $mimetype;
    }
}