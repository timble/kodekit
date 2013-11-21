<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Service Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaControllerModel extends KControllerModel
{
	/**
	 * Constructor
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->getObject('translator')->loadLanguageFiles($this->getIdentifier());

        // Mixin the toolbar interface
        $this->mixin('koowa:controller.toolbar.mixin');

        //Attach the toolbars
        $this->registerCallback('before.render' , array($this, 'attachToolbars'), array($config->toolbars));
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        //Add default toolbars only if the controller is being dispatched and the user is logged in.
        $toolbars = array();
        if($config->dispatched && !JFactory::getUser()->guest)
        {
            $toolbars[] = $this->getIdentifier()->name;

            if($this->getIdentifier()->application === 'admin') {
                $toolbars[] = 'menubar';
            };
        }

        //Set the maximum list limit to 100
        $config->append(array(
            'toolbars'  => $toolbars
        ));

        parent::_initialize($config);
    }

    /**
     * Attach the toolbars to the controller
     *
     * @param array $toolbars A list of toolbars
     * @return ComKoowaControllerView
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
                    ->attachFilter('toolbar', array('toolbars' => $toolbars));
            };
        }

        return $this;
    }

    /**
     * Read action
     *
     * This functions implements an extra check to hide the main menu is the view name is singular (item views)
     *
     * @param  KControllerContextInterface $context A command context object
     * @return KDatabaseRowInterface A row object containing the selected row
     */
    protected function _actionRead(KControllerContextInterface $context)
    {
        //Perform the read action
        $row = parent::_actionRead($context);

        //Add the notice if the row is locked
        if($this->getIdentifier()->application === 'admin' && isset($row))
        {
            if(!isset($this->getRequest()->query->layout) && $row->isLockable() && $row->locked()) {
                JFactory::getApplication()->enqueueMessage($row->lockMessage(), 'notice');
            }
        }

        return $row;
    }
}
