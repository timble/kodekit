<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Behavior Mixin
 *
 * Behaviors are added in FIFO order during construction. Behaviors are added by name and, at runtime behaviors
 * cannot be overridden by attaching a behaviors with the same.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
class KBehaviorMixin extends KCommandMixin implements KBehaviorMixinInterface
{
    /**
     * List of behaviors
     *
     * The key holds the behavior name and the value the behavior object
     *
     * @var array
     */
    private $__behaviors = array();

    /**
     * Auto mixin behaviors
     *
     * @var boolean
     */
    protected $_auto_mixin;

    /**
     * Constructor
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the auto mixin state
        $this->_auto_mixin = $config->auto_mixin;

        //Add the behaviors in FIFO order
        $behaviors = (array) KObjectConfig::unbox($config->behaviors);

        foreach ($behaviors as $key => $value)
        {
            if (is_numeric($key)) {
                $this->addBehavior($value);
            } else {
                $this->addBehavior($key, $value);
            }
        }
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config   An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'behaviors'  => array(),
            'auto_mixin' => true
        ));
    }

    /**
     * Add a behavior
     *
     * @param   mixed $behavior An object that implements BehaviorInterface, an ObjectIdentifier
     *                            or valid identifier string
     * @param   array $config An optional associative array of configuration settings
     * @throws UnexpectedValueException
     * @return  KObject The mixer object
     */
    public function addBehavior($behavior, $config = array())
    {
        //Get the behavior identifier
        if (!($behavior instanceof KBehaviorInterface))
        {
            if (!($behavior instanceof KObjectIdentifier))
            {
                //Create the complete identifier if a partial identifier was passed
                if (is_string($behavior) && strpos($behavior, '.') === false)
                {
                    $identifier = $this->getIdentifier()->toArray();
                    $identifier['path'] = array($identifier['path'][0], 'behavior');
                    $identifier['name'] = $behavior;

                    $identifier = $this->getIdentifier($identifier);
                }
                else $identifier = $this->getIdentifier($behavior);
            }
            else $identifier = $behavior;
        }
        else $identifier = $behavior->getIdentifier();

        //Attach the behavior if it doesn't exist yet
        if(!$this->hasBehavior($identifier->name))
        {
            //Create the behavior object
            if (!($behavior instanceof KBehaviorInterface))
            {
                $config['mixer'] = $this->getMixer();
                $behavior = $this->getObject($identifier, $config);
            }

            if (!($behavior instanceof KBehaviorInterface)) {
                throw new UnexpectedValueException("Behavior $identifier does not implement KBehaviorInterface");
            }

            //Store the behavior to allow for name lookups
            $this->__behaviors[$behavior->getName()] = $behavior;

            //Force set the mixer
            $behavior->setMixer($this->getMixer());

            //Enqueue the behavior
            $this->addCommandHandler($behavior);

            //Mixin the behavior
            if ($this->_auto_mixin) {
                $this->mixin($behavior);
            }
        }

        return $this->getMixer();
    }

    /**
     * Check if a behavior exists
     *
     * @param   string  $name The name of the behavior
     * @return  boolean TRUE if the behavior exists, FALSE otherwise
     */
    public function hasBehavior($name)
    {
        return isset($this->__behaviors[$name]);
    }

    /**
     * Get a behavior by name
     *
     * @param  string  $name   The behavior name
     * @return KBehaviorInterface
     */
    public function getBehavior($name)
    {
        $result = null;

        if(isset($this->__behaviors[$name])) {
            $result = $this->__behaviors[$name];
        }

        return $result;
    }

    /**
     * Gets the behaviors of the table
     *
     * @return array An associative array of table behaviors, keys are the behavior names
     */
    public function getBehaviors()
    {
        return array_values($this->__behaviors);
    }
}