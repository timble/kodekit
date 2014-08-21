<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Model Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Controller
 */
abstract class ComKoowaControllerModel extends KControllerModel
{
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