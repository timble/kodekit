<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Options Provider
 *
 * @author  Israel Canasa <https://github.com/raeldc>
 * @package Koowa\Library\Options
 */
abstract class KOptionsProviderAbstract extends KObject implements KOptionsProviderInterface
{
    /**
     * The list of options by identifier
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Constructor
     *
     * The options array is a hash where the keys are option identifier and the values are a hash of options
     *
     * @param   KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return  KOptionsProviderAbstract
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create the options
        foreach($config->options as $identifier => $data) {
            $this->_options[$identifier] = $this->create($data);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation
     *
     * @param   KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'options' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Loads the options for the given option identifier
     *
     * @param string    $identifier     A unique option identifier
     * @param bool      $refresh        If TRUE and the option has already been loaded it will be re-loaded.
     * @return KOptionsInterface Returns a OptionsInterface object
     */
    public function load($identifier, $refresh = false)
    {
        //Fetch a option from the backend
        if($refresh || !$this->isLoaded($identifier))
        {
            $option = $this->fetch($identifier);
            $this->_options[$identifier] = $option;
        }

        return $this->_options[$identifier];
    }

    /**
     * Fetch the option for the given option identifier from the backend
     *
     * @param string    $identifier     A unique option identifier
     * @return KOptionsInterface|null   Returns a OptionsInterface object or NULL if the option could not be found.
     */
    public function fetch($identifier)
    {
        return $this->create($identifier);
    }

    /**
     * Create a option object
     *
     * @param array $data   An associative array of options data
     * @return KOptionsInterface     Returns a KOptionsInterface object
     */
    public function create($identifier, $data = array())
    {
        $options = $this->getObject('options.default', array('data' => $data));
        return $options;
    }

    /**
     * Store the options object in the provider
     *
     * @param string    $identifier     A unique option identifier
     * @param array     $data           An associative array of option data
     * @return KOptionsInterface     Returns a OptionsInterface object
     */
    public function store($identifier, $data)
    {
        if(!$data instanceof KOptionsInterface) {
            $data = $this->create($identifier, $data);
        }

        $this->_options[$identifier] = $data;

        return true;
    }

    /**
     * Check if options has already been loaded for a given option identifier
     *
     * @param string    $identifier A unique option identifier
     * @return boolean  TRUE if a option has already been loaded. FALSE otherwise
     */
    public function isLoaded($identifier)
    {
        return isset($this->_options[$identifier]);
    }
}
