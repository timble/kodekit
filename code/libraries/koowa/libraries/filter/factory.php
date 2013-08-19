<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Filter Factory
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterFactory extends KObject implements KObjectInstantiatable
{
	/**
     * Force creation of a singleton
     *
     * @param   KObjectConfigInterface  $config     Configuration options
     * @param 	KObjectManagerInterface $container  A KObjectManagerInterface object
     * @return KFilterFactory
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $container)
    {
       // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

	/**
	 * Factory method for KFilterInterface classes.
	 *
	 * @param string $identifier Filter indentifier
	 * @param array  $config Configuration options
	 * @return KFilterAbstract
	 */
	public function instantiate($identifier, $config = array())
	{
		//Get the filter(s) we need to create
		$filters = (array) $identifier;

		//Create the filter chain
		$filter = array_shift($filters);
		$filter = $this->_createFilter($filter, $config);

		foreach($filters as $name) {
			$filter->addFilter(self::_createFilter($name, $config));
		}

		return $filter;
	}

	/**
	 * Create a filter based on it's name
	 *
	 * If the filter is not an identifier this function will create it directly
	 * instead of going through the KObjectManager identification process.
	 *
	 * @param  string$filter Filter identifier
     * @param  array   $config Configuration options
     * @throws	\InvalidArgumentException	When the filter could not be found
     * @throws	\UnexpectedValueException	When the filter does not implement FilterInterface
	 * @return  KFilterInterface
	 */
	protected function _createFilter($filter, $config)
	{
		try
		{
			if(is_string($filter) && strpos($filter, '.') === false ) {
				$filter = 'com:koowa.filter.'.trim($filter);
			}

			$filter = $this->getService($filter, $config);

		} catch(UnexpectedValueException $e) {
			throw new InvalidArgumentException('Invalid filter: '.$filter);
		}

	    //Check the filter interface
		if(!($filter instanceof KFilterInterface))
		{
			$identifier = $filter->getIdentifier();
			throw new UnexpectedValueException("Filter $identifier does not implement KFilterInterface");
		}

		return $filter;
	}
}
