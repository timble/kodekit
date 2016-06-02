<?php
/**
 * Kodekit Component - http://www.timble.net/kodekit
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link		https://github.com/timble/kodekit-pages for the canonical source repository
 */

namespace  Kodekit\Library;

/**
 * Routable View Behavior
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\View\Behavior
 */
class ViewBehaviorRoutable extends ViewBehaviorAbstract
{
    /**
     * A list of entity properties to route
     *
     * URLs will be converted to fully qualified ones in these fields.
     *
     * @var string
     */
    protected $_properties;

    /**
     * Constructor
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_properties = ObjectConfig::unbox($config->properties);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'properties' => array('description'), // Links are converted to absolute ones in these properties
        ));

        parent::_initialize($config);
    }

    /**
     * Register a route() function in the template
     *
     * @param ViewContextInterface $context	A view context object
     * @return void
     */
    protected function _beforeRender(ViewContextInterface $context)
    {
        //Register 'route' method in template
        if($context->subject instanceof ViewTemplatable)
        {
            $context->subject
                ->getTemplate()
                ->registerFunction('route', array($this, 'getRoute'));
        }

        //Fully quality URL's in entity properties
        if($this->getFormat() != 'html')
        {
            foreach($context->entity as $entity)
            {
                foreach($this->_properties as $property)
                {
                    $value = $entity->getProperty($property);

                    if(is_string($value) && !empty($value))
                    {
                        $matches = array();
                        preg_match_all("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", $value, $matches, PREG_SET_ORDER);

                        foreach ($matches as $match)
                        {
                            $route = $this->getObject('lib:dispatcher.router.route', array(
                                'url'    => $match[2],
                                'escape' => $this->getUrl()->isEscaped()
                            ));

                            //Add the host and the schema
                            $route->scheme = $this->getUrl()->scheme;
                            $route->host   = $this->getUrl()->host;

                            $value = str_replace($match[0], $match[1].'="'.$route.'"', $value);
                        }

                        $entity->setProperty($property, $value, false);
                    }
                }
            }
        }
    }

    /**
     * Get a route based on a full or partial query string
     *
     * 'component', 'view' and 'layout' can be omitted. The following variations will all result in the same route :
     *
     * - foo=bar
     * - component=[package]&view=[name]&foo=bar
     *
     * In templates, use route()
     *
     * @param   string|array $route  The query string or array used to create the route
     * @param   boolean      $fqr    If TRUE create a fully qualified route. Defaults to TRUE.
     * @return  DispatcherRouterRoute The route
     */
    public function getRoute($route = '', $fqr = null)
    {
        $parts      = array();
        $identifier = $this->getMixer()->getIdentifier();

        if(is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        //Check to see if there is component information in the route if not add it
        if (!isset($parts['component'])) {
            $parts['component'] = $identifier->package;
        }

        //Add the view information to the route if it's not set
        if (!isset($parts['view'])) {
            $parts['view'] = $this->getMixer()->getName();
        }

        //Add the format information to the route only if it's not 'html'
        if (!isset($parts['format']) && $identifier->name !== 'html') {
            $parts['format'] = $identifier->name;
        }

        //Add the model state and layout only for routes to the same view
        if ($parts['component'] == $identifier->package && $parts['view'] == $this->getMixer()->getName())
        {
            $states = array();
            foreach($this->getModel()->getState() as $name => $state)
            {
                if($state->default != $state->value && !$state->internal) {
                    $states[$name] = $state->value;
                }
            }

            $parts = array_merge($states, $parts);

            //Add the layout information
            if(!isset($parts['layout']) && $this->getMixer() instanceof ViewTemplatable)
            {
                $layout = $this->getLayout();

                if ($layout !== 'default') {
                    $parts['layout'] = $layout;
                }
            }
        }

        //Create the route
        $escape = $this->getUrl()->isEscaped();
        $route  = $this->getObject('lib:dispatcher.router.route', array('escape' =>  $escape))->setQuery($parts);

        //Determine of the url needs to be fully qualified
        if($this->getMixer()->getFormat() == 'html') {
            $fqr = is_bool($fqr) ? $fqr : false;
        } else {
            $fqr = true;
        }

        //Add the host and the schema to qualify relative url
        if ($fqr)
        {
            $route->scheme = $this->getUrl()->scheme;
            $route->host   = $this->getUrl()->host;
        }

        return $route;
    }
}