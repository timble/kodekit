<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller Request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class KControllerRequest extends KObject implements KControllerRequestInterface
{
    /**
     * The request query
     *
     * @var KObjectArray
     */
    protected $_query;

    /**
     * Constructor
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set query parameters
        $this->setQuery($config->query);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'query' => array()
        ));

        parent::_initialize($config);
    }

    /**
     * Set the request query
     *
     * @param  array $query
     * @return KControllerRequest
     */
    public function setQuery($query)
    {
        $this->_query = $this->getObject('koowa:object.array', array('data' => $query));
        return $this;
    }

    /**
     * Get the request query
     *
     * @return KObjectArray
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Implement a virtual 'headers', 'query' and 'data class property to return their respective objects.
     *
     * @param   string $name  The property name.
     * @return  mixed The property value.
     */
    public function __get($name)
    {
        $result = null;

        if($name == 'query') {
            $result = $this->getQuery();
        }

        return $result;
    }

    /**
     * Deep clone of this instance
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();

        $this->_query = clone $this->_query;
    }
}