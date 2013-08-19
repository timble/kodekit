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
        $this->mixin(new KControllerToolbarMixin($config->append(array('mixer' => $this))));

        //Attach the toolbars
        $this->registerCallback('before.get' , array($this, 'attachToolbars'));
    }

    /**
     * Attach the toolbars to the controller
     *
     * void
     */
    public function attachToolbars()
    {
        if($this->getView() instanceof KViewHtml)
        {
            if($this->isDispatched() && !JFactory::getUser()->guest)
            {
                $this->attachToolbar($this->getView()->getName());

                if($this->getIdentifier()->application === 'admin') {
                    $this->attachToolbar('menubar');
                };
            }

            if($toolbars = $this->getToolbars())
            {
                $this->getView()
                    ->getTemplate()
                    ->addFilter('toolbar', array('toolbars' => $toolbars));
            };
        }
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
        $this->getService('translator')->loadLanguageFiles($this->getIdentifier());
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
