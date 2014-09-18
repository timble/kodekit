<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Decorator
 *
 * The object decorator implements the same interface as KObject and can only be used to decorate objects extending from
 * KObject. To decorate an object that does not extend from KObject use KObjectDecoratorAbstract instead.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Decorator
 */
abstract class KObjectDecorator extends KObjectDecoratorAbstract implements KObjectInterface, KObjectMixable, KObjectDecoratable
{
    /**
     * Checks if the decorated object or one of it's mixin's inherits from a class.
     *
     * @param   string|object $class  The class to check
     * @return  boolean  Returns TRUE if the object inherits from the class
     */
    public function inherits($class)
    {
        $delegate = $this->getDelegate();

        if ($delegate instanceof KObjectMixable) {
            $result = $delegate->inherits($class);
        } else {
            $result = $delegate instanceof $class;
        }

        return $result;
    }

    /**
     * Mixin an object
     *
     * When using mixin(), the calling object inherits the methods of the mixed in objects, in a LIFO order.
     *
     * @param   mixed $mixin   A KObjectIdentifier, identifier string or object implementing KObjectMixinInterface
     * @param   array $config  An optional associative array of configuration options
     * @return  KObjectDecorator
     */
    public function mixin($mixin, $config = array())
    {
        $this->getDelegate()->mixin($mixin, $config);
        return $this;
    }

    /**
     * Decorate the object
     *
     * When using decorate(), the decorator will be re-decorated. The decorator needs to extend from
     * ObjectDecorator.
     *
     * @param   mixed $decorator An KObjectIdentifier, identifier string or object implementing KObjectDecorator
     * @param   array $config    An optional associative array of configuration options
     * @return  KObjectDecoratorInterface
     * @throws  KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws  UnexpectedValueException If the decorator does not extend from ObjectDecorator
     */
    public function decorate($decorator, $config = array())
    {
        $decorator = $this->getDelegate()->decorate($decorator, $config);

        //Notify the decorator and set the delegate
        $decorator->onDecorate($this);

        return $decorator;
    }

    /**
     * Set the decorated object
     *
     * @param   KObjectInterface $delegate The object to decorate
     * @return  KObjectDecorator
     * @throws  InvalidArgumentException If the delegate does not extend from KObject
     */
    public function setDelegate($delegate)
    {
        if (!$delegate instanceof KObject) {
            throw new InvalidArgumentException('Delegate needs to extend from KObject');
        }

        return parent::setDelegate($delegate);
    }

    /**
     * Get an instance of a class based on a class identifier only creating it if it does not exist yet.
     *
     * @param  mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @param  array $config     An optional associative array of configuration settings.
     * @return	Object Return object on success, throws exception on failure
     */
    public function getObject($identifier, array $config = array())
    {
        return $this->getDelegate()->getObject($identifier, $config);
    }

    /**
     * Get an object identifier.
     *
     * @param  mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return KObjectIdentifier
     */
    public function getIdentifier($identifier = null)
    {
        return $this->getDelegate()->getIdentifier($identifier);
    }

    /**
     * Get the object configuration
     *
     * @param  mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return KObjectConfig
     */
    public function getConfig($identifier = null)
    {
        return $this->getDelegate()->getConfig($identifier);
    }
}
