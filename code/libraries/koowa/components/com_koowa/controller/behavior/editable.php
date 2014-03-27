<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Editable Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class ComKoowaControllerBehaviorEditable extends KControllerBehaviorEditable
{
    /**
     * Saves the current row and redirects to a new edit form
     *
     * @param KControllerContextInterface $context
     * @return KModelEntityInterface
     */
    protected function _actionSave2new(KControllerContextInterface $context)
    {
        // Cache and lock the referrer since _ActionSave would unset it
        $referrer = $this->getReferrer($context);
        $this->_lockReferrer($context);

        $entity = $this->save($context);

        // Re-set the referrer
        $cookie = $this->getObject('lib:http.cookie', array(
            'name'   => 'referrer',
            'value'  => (string) $referrer,
            'path'   => $this->_cookie_path
        ));

        $context->response->headers->addCookie($cookie);

        $identifier = $this->getMixer()->getIdentifier();
        $view       = KStringInflector::singularize($identifier->name);
        $url        = sprintf('index.php?option=com_%s&view=%s', $identifier->package, $view);

        $context->response->setRedirect($this->getObject('lib:http.url',array('url' => $url)));

        return $entity;
    }

    /**
     * Add a lock flash message if the resource is locked
     *
     * @param   KControllerContext	$context A command context object
     * @return 	void
     */
    protected function _afterRead(KControllerContext $context)
    {
        $entity = $context->result;

        //Add the notice if the resource is locked
        if($this->canEdit() && $entity->isLockable() && $entity->isLocked())
        {
            //Prevent a re-render of the message
            if($context->request->getUrl() != $context->request->getReferrer())
            {
                if($entity->isLockable() && $entity->isLocked())
                {
                    $user = $this->getObject('user.provider')->load($entity->locked_by);

                    $date    = $this->getObject('date', array('date' => $entity->locked_on));
                    $message = $this->getObject('translator')->translate(
                        'Locked by {name} {date}', array('name' => $user->getName(), 'date' => $date->humanize())
                    );

                    $context->response->addMessage($message, 'notice');
                }
            }
        }
    }

    /**
     * Only lock entities in administrator or in form layouts in site
     *
     * {@inheritdoc}
     */
    protected function _lockResource(KControllerContextInterface $context)
    {
        $domain = $this->getMixer()->getIdentifier()->domain;

        if ($domain === 'admin' || $this->getRequest()->query->layout === 'form') {
            parent::_lockResource($context);
        }
    }

    /**
     * Only unlock entities in administrator or in form layouts in site
     *
     * {@inheritdoc}
     */
    protected function _unlockResource(KControllerContextInterface $context)
    {
        $domain = $this->getMixer()->getIdentifier()->domain;

        if ($domain === 'admin' || $this->getRequest()->query->layout === 'form') {
            parent::_unlockResource($context);
        }
    }
}
