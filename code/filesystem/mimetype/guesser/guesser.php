<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 *
 */

/**
 * A singleton mime type guesser.
 *
 * By default, all mime type guessers provided by the framework are installed
 * (if available on the current OS/PHP setup).
 *
 * You can register custom guessers by calling the register() method.
 * Custom guessers are always called before any default ones.
 *
 *     $guesser = $this->getObject('filesystem.mimetype.guesser');
 *     $guesser->register('custom.guesser.identifier');
 *
 * If you want to change the order of the default guessers, just re-register your
 * preferred one as a custom one. The last registered guesser is preferred over
 * previously registered ones.
 *
 * Re-registering a built-in guesser also allows you to configure it:
 *
 *     $guesser = $this->getObject('filesystem.mimetype.guesser');
 *     $guesser->register($this->getObject('filesystem.mimetype.guesser.fileinfo', array(
 *         'magic_file' => '/path/to/magic/file'
 *     )));
 *
 * This class is based on Symfony 2 class Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser
 * and subject to MIT license
 * Copyright (c) Fabien Potencier <fabien@symfony.com>
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Filesystem\Mimetype\Guesser
 */
class KFilesystemMimetypeGuesser extends KObject implements KFilesystemMimetypeGuesserInterface
{
    /**
     * All registered guesser instances.
     *
     * @var array
     */
    protected $_guessers = array();

    /**
     * Registers all natively provided mime type guessers.
     *
     * {@inheritdoc}
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->register('extension');
        $this->register('binary');
        $this->register('fileinfo');
    }

    /**
     * {@inheritdoc}
     */
    public static function isSupported()
    {
        return true;
    }

    /**
     * Removes all registered guessers
     *
     * @return $this
     */
    public function reset()
    {
        $this->_guessers = array();

        return $this;
    }

    /**
     * Registers a new mime type guesser.
     *
     * When guessing, this guesser is preferred over previously registered ones.
     *
     * @param string|KFilesystemMimetypeGuesserInterface $guesser
     * @return $this
     */
    public function register($guesser)
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($guesser) && strpos($guesser, '.') === false)
        {
            if (strpos($guesser, '.') === false)
            {
                $identifier = $this->getIdentifier()->toArray();
                $identifier['path'][] = 'guesser';
                $identifier['name'] = $guesser;

                $guesser = $this->getObject($identifier);
            }
            else {
                $guesser = $this->getObject($guesser);
            }
        }

        if (!$guesser instanceof KFilesystemMimetypeGuesserInterface) {
            throw new UnexpectedValueException("Guesser does not implement KFilesystemMimetypeGuesserInterface");
        }

        array_unshift($this->_guessers, $guesser);

        return $this;
    }

    /**
     * Tries to guess the mime type of the given file.
     *
     * The file is passed to each registered mime type guesser in reverse order
     * of their registration (last registered is queried first). Once a guesser
     * returns a value that is not NULL, this method terminates and returns the
     * value.
     *
     * @param string $path The path to the file
     *
     * @return string The mime type or NULL, if none could be guessed
     *
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function guess($path)
    {
        if (!is_file($path)) {
            throw new \RuntimeException('File not found at '.$path);
        }

        if (!is_readable($path)) {
            throw new \RuntimeException('File not readable at '.$path);
        }

        foreach ($this->_guessers as $guesser)
        {
            /* @var $guesser KFilesystemMimetypeGuesserInterface */
            if (null !== $mimeType = $guesser->guess($path)) {
                return $mimeType;
            }
        }

        return null;
    }
}