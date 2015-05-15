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
 * Mime type finder chain
 *
 * You can register custom finders by calling the add() method.
 * Custom finders are always called before any default ones.
 *
 *     $mimetype = $this->getObject('filesystem.mimetype');
 *     $mimetype->add('custom.finder.identifier');
 *
 * If you want to change the order of the default finders, just re-register your
 * preferred one as a custom one. The last registered finder is preferred over
 * previously registered ones.
 *
 * Re-registering a built-in finder also allows you to configure it:
 *
 *     $mimetype = $this->getObject('filesystem.mimetype');
 *     $mimetype->add($this->getObject('filesystem.mimetype.fileinfo', array(
 *         'magic_file' => '/path/to/magic/file'
 *     )));
 *
 * This class is based on Symfony 2 class Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser
 * and subject to MIT license
 * Copyright (c) Fabien Potencier <fabien@symfony.com>
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Filesystem\Mimetype
 */
class KFilesystemMimetype extends KObject implements KFilesystemMimetypeInterface
{
    /**
     * All registered finder instances.
     *
     * @var array
     */
    protected $_finders = array();

    /**
     * Registers all natively provided mime type finders.
     *
     * {@inheritdoc}
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        foreach ($config->finders as $finder) {
            $this->add($finder);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function _initialize(KObjectConfig $config)
    {
        if (empty($config->finders))
        {
            $config->append(array(
                'finders' => array('extension')
            ));
        }

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
     * Removes all registered finders
     *
     * @return $this
     */
    public function reset()
    {
        $this->_finders = array();

        return $this;
    }

    /**
     * Registers a new mime type finder.
     *
     * The last added finder is preferred over previously registered ones.
     *
     * @param string|KFilesystemMimetypeInterface $finder
     * @return $this
     */
    public function add($finder)
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($finder) && strpos($finder, '.') === false)
        {
            if (strpos($finder, '.') === false)
            {
                $identifier = $this->getIdentifier()->toArray();
                $identifier['path'][] = 'mimetype';
                $identifier['name'] = $finder;

                $finder = $this->getObject($identifier);
            }
            else {
                $finder = $this->getObject($finder);
            }
        }

        if (!$finder instanceof KFilesystemMimetypeInterface) {
            throw new UnexpectedValueException("Mimetype object does not implement KFilesystemMimetypeInterface");
        }

        array_unshift($this->_finders, $finder);

        return $this;
    }

    /**
     * Tries to guess the mime type of the given file.
     *
     * The file is passed to each registered mime type finder in reverse order
     * of their registration (last registered is queried first). Once a finder
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
    public function find($path)
    {
        if (!is_file($path)) {
            throw new \RuntimeException('File not found at '.$path);
        }

        if (!is_readable($path)) {
            throw new \RuntimeException('File not readable at '.$path);
        }

        foreach ($this->_finders as $finder)
        {
            /* @var $finder KFilesystemMimetypeInterface */
            if (null !== $mimeType = $finder->find($path)) {
                return $mimeType;
            }
        }

        return null;
    }
}