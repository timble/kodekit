<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Resource Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaControllerResource extends KControllerResource
{
    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_limit = $config->limit;

        // Mixin the toolbar interface
        $this->mixin(new KControllerToolbarMixin(array('mixer' => $this)));

        //Attach the toolbars
        $this->registerCallback('before.get' , array($this, 'attachToolbars'), array($config->toolbars));
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $toolbars = array();
        if($config->dispatched() && !JFactory::getUser()->guest)
        {
            $toolbars[] = $this->getIdentifier()->name;

            if($this->getIdentifier()->application === 'admin') {
                $toolbars[] = 'menubar';
            };
        }

        $config->append(array(
            'toolbars'  => $toolbars
        ));

        parent::_initialize($config);
    }

    /**
     * Attach the toolbars to the controller
     *
     * @param array $toolbars A list of toolbars
     * @return ComKoowaControllerResource
     */
    public function attachToolbars($toolbars)
    {
        if($this->getView() instanceof KViewHtml)
        {
            foreach($toolbars as $toolbar) {
                $this->attachToolbar($toolbar);
            }

            if($toolbars = $this->getToolbars())
            {
                $this->getView()
                    ->getTemplate()
                    ->addFilter('toolbar', array('toolbars' => $toolbars));
            };
        }

        return $this;
    }

    /**
     * Display action
     *
     * If the controller was not dispatched manually load the languages files
     *
     * @param   KCommandContext $context A command context object
     * @return 	string|bool 	The rendered output of the view or false if something went wrong
     */
    protected function _actionGet(KCommandContext $context)
    {
        $this->getObject('translator')->loadLanguageFiles($this->getIdentifier());
        return parent::_actionGet($context);
    }

	/**
     * Set a request property
     *
     *  This function translates 'limitstart' to 'offset' for compatibility with Joomla
     *
     * @param  	string 	$property The property name.
     * @param 	mixed 	$value    The property value.
     */
 	public function __set($property, $value)
    {
        if($property == 'limitstart') {
            $property = 'offset';
        }

        parent::__set($property, $value);
  	}
}
