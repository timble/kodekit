<?php
/**
 * Kodekit Component - http://www.timble.net/kodekit
 *
 * @copyright	Copyright (C) 2011 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link		https://github.com/timble/kodekit-pages for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Decoratable Dispatcher Behavior
 *
 * This behavior will inject following view parameters automatically:
 *
 * - component: The name of the component being dispatched
 * - language: The language of the response
 * - status: The status code of the response
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Component\Pages
 */
class DispatcherBehaviorDecoratable extends DispatcherBehaviorAbstract
{
    /**
     * The decorators
     *
     * @var array
     */
    private $__decorators = array();

    /**
     * Constructor
     *
     * @param ObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Register the decorators
        foreach($config->decorators as $decorator) {
            $this->addDecorator($decorator);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'decorators' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer   = $this->getMixer();
        $request = $mixer->getRequest();

        if($request->getFormat() == 'html' && $request->isGet() && !$request->isAjax()) {
            return true;
        }

        return false;
    }

    /**
     * Add a decorator
     *
     * @param  string $identifier The decorator identifier
     * @param  bool $prepend      If true, the decorator will be prepended instead of appended.
     * @return DispatcherBehaviorDecoratable
     */
    public function addDecorator($identifier, $prepend = false)
    {
        if($prepend) {
            array_unshift($this->__decorators, $identifier);
        } else {
            array_push($this->__decorators, $identifier);
        }

        return $this;
    }

    /**
     * Get the list of decorators
     *
     * @return array The decorators
     */
    public function getDecorators()
    {
        return $this->__decorators;
    }

    /**
     * Render the decorators
     *
     * This method will also add the 'decorator' filter to the view and add following default parameters
     *
     * - component: The name of the component being dispatched
     * - language: The language of the response
     * - status: The status code of the response
     *
     * @param   DispatcherContext $context The active command context
     * @return  void
     */
    protected function _beforeSend(DispatcherContext $context)
    {
        $response = $context->getResponse();

        if(!$response->isDownloadable())
        {
            foreach ($this->getDecorators() as $decorator)
            {
                //Get the decorator
                $config     = array('response' => array('content' => $response->getContent()));
                $controller = $this->getObject($decorator, $config);

                if(!$controller instanceof ControllerViewable)
                {
                    throw new \UnexpectedValueException(
                        'Decorator '.get_class($controller).' does not implement ControllerViewable'
                    );
                }

                //Set the view
                $parameters = array(
                    'language' => $this->getLanguage(),
                );

                if($response->isError()) {
                    $parameters['status'] = $response->getStatusCode();
                } else {
                    $parameters['component'] = $this->getController()->getIdentifier()->package;
                }

                $controller->getView()
                    ->setParameters($parameters)
                    ->getTemplate()->addFilter('decorator');

                //Set the response
                $response->setContent($controller->render());
            }
        }
    }
}