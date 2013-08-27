<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Filter
 *
 * If the filter implements KFilterTraversable it will be decorated with KFilterIterator to allow iterating over the data
 * being filtered in case of an array of a Traversable object. If a filter does not implement KFilterTraversable the data
 * will be passed directly to the filter.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
abstract class KFilterAbstract extends KObject implements KFilterInterface, KObjectInstantiatable
{
    /**
     * The filter errors
     *
     * @var	array
     */
    protected $_errors = array();

    /**
     * Force creation of a singleton
     *
     * Function also decorates the filter with KFilterIterator if the filter implements KFilterTraversable
     *
     * @param   KObjectConfigInterface  $config    Configuration options
     * @param 	KObjectManagerInterface $manager A KObjectManagerInterface object
     * @return KFilterInterface
     * @see KFilterTraversable
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        // Check if an instance with this identifier already exists or not
        if (!$manager->isRegistered($config->object_identifier))
        {
            //Create the singleton
            $classname = $config->object_identifier->classname;
            $instance  = new $classname($config);

            if($instance instanceof KFilterTraversable) {
                $instance = $instance->decorate('koowa:filter.iterator');
            }

            $manager->setObject($config->object_identifier, $instance);
        }

        return $manager->getObject($config->object_identifier);
    }

    /**
     * Validate a scalar or traversable value
     *
     * NOTE: This should always be a simple yes/no question (is $value valid?), so only true or false should be returned
     *
     * @param   mixed   $value Value to be validated
     * @return  bool    True when the value is valid. False otherwise.
     */
    public function validate($value)
    {
        return false;
    }

    /**
     * Sanitize a scalar or traversable value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  mixed   The sanitized value
     */
    public function sanitize($value)
    {
        return $value;
    }

    /**
     * Get a list of error that occurred during sanitize or validate
     *
     * @return array
     */
    public function getErrors()
    {
        return (array) $this->_errors;
    }

    /**
     * Add an error message
     *
     * @param $message
     * @return boolean Returns false
     */
    protected function _error($message)
    {
        $this->_errors[] = $message;
        return false;
    }
}
