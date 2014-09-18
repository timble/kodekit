<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Exception Event Publisher
 *
 * Exception publisher will publish an 'onException' event wrapping the Exception as a EventException and passing it to all
 * the listeners.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event\Publisher
 */
class KEventPublisherException extends KEventPublisherAbstract
{
    /**
     * The exception handler
     *
     * @var KExceptionHandlerInterface
     */
    private $__exception_handler;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->__exception_handler = $config->exception_handler;

        if($this->isEnabled()) {
            $this->enable();
        }
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
           'exception_handler' => 'exception.handler'
        ));

        parent::_initialize($config);
    }

    /**
     * Enable the publisher
     *
     * @return  KEventPublisherException
     */
    public function enable()
    {
        $this->getExceptionHandler()->addHandler(array($this, 'publishException'));
        return parent::enable();
    }

    /**
     * Disable the publisher
     *
     * @return  KEventPublisherException
     */
    public function disable()
    {
        $this->getExceptionHandler()->removeHandler(array($this, 'publishException'));
        return parent::enable();
    }

    /**
     * Publish an 'onException' event by calling all listeners that have registered to receive it.
     *
     * @param   Exception           $exception  The exception to be published.
     * @param  array|Traversable    $attributes An associative array or a Traversable object
     * @param  mixed                $target     The event target
     * @return  KEventException
     */
    public function publishException(Exception $exception, $attributes = array(), $target = null)
    {
        //Make sure we have an event object
        $event = new KEventException('onException', $attributes, $target);
        $event->setException($exception);

        parent::publishEvent($event);
    }

    /**
     * Get the chain of command object
     *
     * @throws UnexpectedValueException
     * @return KExceptionHandlerInterface
     */
    public function getExceptionHandler()
    {
        if(!$this->__exception_handler instanceof KExceptionHandlerInterface)
        {
            $this->__exception_handler = $this->getObject($this->__exception_handler);

            if(!$this->__exception_handler instanceof KExceptionHandler)
            {
                throw new UnexpectedValueException(
                    'Exception Handler: '.get_class($this->__exception_handler).' does not implement KExceptionHandlerInterface'
                );
            }
        }

        return $this->__exception_handler;
    }

    /**
     * Set the exception handler object
     *
     * @param   KExceptionHandlerInterface $handler An exception handler object
     * @return  KEventPublisherException
     */
    public function setExceptionHandler(KExceptionHandlerInterface $handler)
    {
        $this->__exception_handler = $handler;

        //Re-enable the exception handler
        if($this->isEnabled())
        {
            $this->disable();
            $this->enable();
        }

        return $this;
    }
}