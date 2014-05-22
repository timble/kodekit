<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Model Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
abstract class ComKoowaControllerModel extends KControllerModel
{
	/**
	 * Constructor
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->getObject('translator')->loadTranslations($this->getIdentifier());

        // Mixin the toolbar interface
        $this->mixin('lib:controller.toolbar.mixin');

        //Attach the toolbars
        $this->addCommandCallback('before.render', '_addToolbars', array('toolbars' => $config->toolbars));
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
        $toolbars[] = $this->getIdentifier()->name;

        if($this->getIdentifier()->domain === 'admin') {
            $toolbars[] = 'menubar';
        };

        $config->append(array(
            'toolbars'   => $toolbars,
            'behaviors'  => array('editable', 'persistable'),
        ));

        parent::_initialize($config);
    }

    /**
     * Add the toolbars to the controller
     *
     * @param KControllerContextInterface $context
     */
    protected function _addToolbars(KControllerContextInterface $context)
    {
        if($this->getView() instanceof KViewHtml)
        {
            if($context->getUser()->isAuthentic() && $this->isDispatched())
            {
                foreach($context->toolbars as $toolbar) {
                    $this->addToolbar($toolbar);
                }

                if($toolbars = $this->getToolbars())
                {
                    $this->getView()
                        ->getTemplate()
                        ->attachFilter('toolbar', array('toolbars' => $toolbars));
                };
            }
        }
    }

    /**
     * Generic read action, fetches an item
     *
     * @param  KControllerContextInterface $context A command context object
     * @throws KControllerExceptionResourceNotFound
     * @return KModelEntityInterface
     */
    protected function _actionRead(KControllerContextInterface $context)
    {
        //Request
        if($this->getIdentifier()->domain === 'admin')
        {
            if($this->isEditable() && KStringInflector::isSingular($this->getView()->getName()))
            {
                //Use JInput as we do not pass the request query back into the Joomla context
                JFactory::getApplication()->input->set('hidemainmenu', 1);
            }
        }

        return parent::_actionRead($context);
    }
}
