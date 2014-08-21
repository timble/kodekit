<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Toolbar Mixin
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar
 */
class KControllerToolbarMixin extends KObjectMixinAbstract implements KObjectMixinInterface
{
    /**
     * List of toolbars
     *
     * The key holds the toolbar type and the value the toolbar object
     *
     * @var    array
     */
    private $__toolbars = array();

    /**
     * Constructor
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Add the toolbars
        $toolbars = (array)KObjectConfig::unbox($config->toolbars);

        foreach ($toolbars as $key => $value)
        {
            if (is_numeric($key)) {
                $this->addToolbar($value);
            } else {
                $this->addToolbar($key, $value);
            }
        }
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'toolbars' => array(),
        ));
    }

    /**
     * Add a toolbar
     *
     * @param   mixed $toolbar An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param  array  $config An optional associative array of configuration settings
     * @throws UnexpectedValueException
     * @return  KObject The mixer object
     */
    public function addToolbar($toolbar, $config = array())
    {
        if (!($toolbar instanceof KControllerToolbarInterface))
        {
            if (!($toolbar instanceof KObjectIdentifier))
            {
                //Create the complete identifier if a partial identifier was passed
                if (is_string($toolbar) && strpos($toolbar, '.') === false)
                {
                    $identifier = $this->getIdentifier()->toArray();
                    $identifier['path'] = array('controller', 'toolbar');
                    $identifier['name'] = $toolbar;

                    $identifier = $this->getIdentifier($identifier);
                }
                else $identifier = $this->getIdentifier($toolbar);
            }
            else $identifier = $toolbar;

            $config['controller'] = $this->getMixer();
            $toolbar = $this->getObject($identifier, $config);
        }

        if (!($toolbar instanceof KControllerToolbarInterface)) {
            throw new UnexpectedValueException("Controller toolbar $identifier does not implement KControllerToolbarInterface");
        }

        //Store the toolbar to allow for type lookups
        $this->__toolbars[$toolbar->getType()] = $toolbar;

        if ($this->inherits('KCommandMixin')) {
            $this->addCommandHandler($toolbar);
        }

        return $this->getMixer();
    }

    /**
     * Remove a toolbar
     *
     * @param   KControllerToolbarInterface $toolbar A toolbar instance
     * @return  Object The mixer object
     */
    public function removeToolbar(KControllerToolbarInterface $toolbar)
    {
        if($this->hasToolbar($toolbar->getType()))
        {
            unset($this->__toolbars[$toolbar->getType()]);

            if ($this->inherits('KCommandMixin')) {
                $this->removeCommandHandler($toolbar);
            }
        }

        return $this->getMixer();
    }

    /**
     * Check if a toolbar exists
     *
     * @param   string   $type The name of the toolbar
     * @return  boolean  TRUE if the toolbar exists, FALSE otherwise
     */
    public function hasToolbar($type = 'actionbar')
    {
        return isset($this->__toolbars[$type]);
    }

    /**
     * Get a toolbar by type
     *
     * @param  string  $type   The toolbar name
     * @return KControllerToolbarInterface
     */
    public function getToolbar($type = 'actionbar')
    {
        $result = null;

        if(isset($this->__toolbars[$type])) {
            $result = $this->__toolbars[$type];
        }

        return $result;
    }

    /**
     * Gets the toolbars
     *
     * @return array  An array of toolbars
     */
    public function getToolbars()
    {
        return array_values($this->__toolbars);
    }
}