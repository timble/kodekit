<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Filter Factory
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterFactory extends KObject implements KObjectSingleton
{
    /**
     * Factory method for KFilterChain classes.
     *
     * Method accepts an array of filter names, or filter object identifiers and will create a chained filter
     * using a FIFO approach.
     *
     * @param   string|array $identifier Filter identifier(s)
     * @param   object|array $config     An optional KObjectConfig object with configuration options
     * @return  KFilterInterface
     */
    public function createChain($identifier, $config = array())
    {
        //Get the filter(s) we need to create
        $filters = (array) $identifier;
        $chain   = $this->getObject('lib:filter.chain');

        foreach($filters as $name)
        {
            $instance = $this->createFilter($name, $config);
            $chain->addFilter($instance);
        }

        return $chain;
    }

    /**
     * Factory method for KFilter classes.
     *
     * If the filter is not an identifier this function will create it directly instead of going through the KObject
     * identification process.
     *
     * @param   string  $filter Filter identifier
     * @param   object|array $config     An optional KObjectConfig object with configuration options
     * @throws	UnexpectedValueException	When the filter does not implement FilterInterface
     * @return  KFilterInterface
     */
    public function createFilter($filter, $config = array())
    {
        if(is_string($filter) && strpos($filter, '.') === false )
        {
            $identifier = $this->getIdentifier()->toArray();
            $identifier['name'] = $filter;
        }
        else $identifier = $filter;

        $filter = $this->getObject($identifier, $config);

        //Check the filter interface
        if(!($filter instanceof KFilterInterface)) {
            throw new UnexpectedValueException('Filter:'.get_class($filter).' does not implement FilterInterface');
        }

        return $filter;
    }

    /**
     * Allow for filter chaining
     *
     * @param  string   $method    The function name
     * @param  array    $arguments The function arguments
     * @return mixed The result of the function
     * @throws BadMethodCallException   If method could not be found
     */
    public function __call($method, $arguments)
    {
        return $this->createChain($method, $arguments);
    }
}
