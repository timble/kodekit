<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * FileSystem String Stream Wrapper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filesystem
 */
abstract class KFilesystemStreamWrapperAbstract extends KObject implements KFilesystemStreamWrapperInterface
{
    /**
     * The wrapper type
     *
     * @var string
     */
    protected $_type;

    /**
     * The wrapper path
     *
     * @var string
     */
    protected $_path;

    /**
     * Current stream position
     *
     * @var int
     */
    protected $_position;

    /**
     * The stream mode
     *
     * @var boolean
     */
    protected $_mode;

    /**
     * Whether this stream can be read
     *
     * @var boolean
     */
    protected $_read;

    /**
     * Whether this stream can be written
     *
     * @var boolean
     */
    protected $_write;

    /**
     * Options
     *
     * @var int
     */
    protected $_options;

    /**
     * Object constructor
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config = null)
    {
        //If stream is being constructed through object manager call parent.
        if($config instanceof KObjectConfig)
        {
            parent::__construct($config);

            $this->_type = $config->type;
        }
    }

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
            'type' => KFilesystemStreamInterface::TYPE_UNKNOWN
        ));
    }

    /**
     * Get the stream type
     *
     * @return string The stream type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the stream path
     *
     * @return string The stream protocol
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set the stream options
     *
     * @return string The stream options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set the stream options
     *
     * @param string $options Set the stream options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * Set the stream mode
     *
     * @return string The stream mode
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Set the stream mode
     *
     * @param $mode
     * @return boolean|null
     */
    public function setMode($mode)
    {
        $this->_mode = $mode; //store the raw mode

        //Strip binary/text flags from mode for comparison
        $mode = strtr($mode, array('b' => '', 't' => ''));

        switch ($mode)
        {
            case 'r':
                $this->_read     = true;
                $this->_write    = false;
                $this->_position = 0;
                break;

            case 'r+':
            case 'c+':
                $this->_read     = true;
                $this->_write    = true;
                $this->_position = 0;
                break;

            case 'w':
                $this->_read     = false;
                $this->_write    = true;
                $this->_position = 0;
                $this->stream_truncate(0);
                break;

            case 'w+':
                $this->_read      = true;
                $this->_write     = true;
                $this->_position  = 0;
                $this->stream_truncate(0);
                break;

            case 'a':
                $this->_read     = false;
                $this->_write    = true;
                $this->_position = strlen($this->_data);
                break;

            case 'a+':
                $this->_read     = true;
                $this->_write    = true;
                $this->position  = strlen($this->_data);
                break;

            case 'c':
                $this->_read     = false;
                $this->_write    = true;
                $this->_position = 0;
                break;

            default:
                if ($this->_options & STREAM_REPORT_ERRORS) {
                    trigger_error('Invalid mode specified (mode specified makes no sense for this stream implementation)', E_ERROR);
                } else {
                    return false;
                }
        }
    }
}