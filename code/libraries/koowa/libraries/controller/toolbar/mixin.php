<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller Toolbar Mixin
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class KControllerToolbarMixin extends KObjectMixinAbstract
{
    /**
     * List of toolbars
     *
     * The key holds the behavior name and the value the behavior object
     *
     * @var    array
     */
    protected $_toolbars = array();

    /**
     * Constructor
     *
     * @param KConfig $config  An optional ObjectConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Add the toolbars
        $toolbars = (array)KConfig::unbox($config->toolbars);

        foreach ($toolbars as $key => $value)
        {
            if (is_numeric($key)) {
                $this->attachToolbar($value);
            } else {
                $this->attachToolbar($key, $value);
            }
        }
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'toolbars' => array(),
        ));
    }

    /**
     * Attach a toolbar
     *
     * @param   mixed $toolbar An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param  array  $config   An optional associative array of configuration settings
     * @return  KObject The mixer object
     */
    public function attachToolbar($toolbar, $config = array())
    {
        if (!($toolbar instanceof KControllerToolbarInterface)) {
            $toolbar = $this->createToolbar($toolbar, $config);
        }

        //Store the toolbar to allow for type lookups
        $this->_toolbars[$toolbar->getType()] = $toolbar;

        if ($this->inherits('KCommandMixin')) {
            $this->getCommandChain()->enqueue($toolbar);
        }

        return $this->getMixer();
    }

    /**
     * Check if a toolbar exists
     *
     * @param   string   $toolbar The name of the toolbar
     * @return  boolean  TRUE if the toolbar exists, FALSE otherwise
     */
    public function hasToolbar($type = 'actionbar')
    {
        return isset($this->_toolbars[$type]);
    }

    /**
     * Get a toolbar by type
     *
     * @param  string  $name   The toolbar name
     * @return KControllerToolbarInterface
     */
    public function getToolbar($type = 'actionbar')
    {
        $result = null;

        if(isset($this->_toolbars[$type])) {
            $result = $this->_toolbars[$type];
        }

        return $result;
    }

    /**
     * Gets the toolbars
     *
     * @return array  An associative array of toolbars, keys are the toolbar names
     */
    public function getToolbars()
    {
        return $this->_toolbars;
    }

    /**
     * Get a toolbar by identifier
     *
     * @return KControllerToolbarInterface
     */
    public function createToolbar($toolbar, $config = array())
    {
        if (!($toolbar instanceof KServiceIdentifier))
        {
            //Create the complete identifier if a partial identifier was passed
            if (is_string($toolbar) && strpos($toolbar, '.') === false)
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('controller', 'toolbar');
                $identifier->name = $toolbar;
            }
            else $identifier = $this->getIdentifier($toolbar);
        }
        else $identifier = $toolbar;

        $config['controller'] = $this->getMixer();
        $toolbar = $this->getService($identifier, $config);

        if (!($toolbar instanceof KControllerToolbarInterface)) {
            throw new \UnexpectedValueException("Controller toolbar $identifier does not implement KControllerToolbarInterface");
        }

        return $toolbar;
    }
}