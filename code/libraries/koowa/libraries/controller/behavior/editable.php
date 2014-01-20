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
class KControllerBehaviorEditable extends KControllerBehaviorAbstract
{
    protected $_cookie_path;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if($this->isDispatched() && $this->getRequest()->getFormat() == 'html')
        {
            $this->addCommandHandler('before.read' , 'setReferrer');
            $this->addCommandHandler('after.apply' , '_lockReferrer');
            $this->addCommandHandler('after.read'  , '_unlockReferrer');
            $this->addCommandHandler('after.save'  , '_unsetReferrer');
            $this->addCommandHandler('after.cancel', '_unsetReferrer');

            $this->addCommandHandler('after.read'  , '_lockResource');
            $this->addCommandHandler('after.save'  , '_unlockResource');
            $this->addCommandHandler('after.cancel', '_unlockResource');
        }

        $this->_cookie_path = $config->cookie_path;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'cookie_path' => $this->getObject('request')->getBaseUrl()->toString(KHttpUrl::PATH).'/'
        ));

        parent::_initialize($config);
    }

    /**
     * Get the referrer
     *
     * @param   KControllerContextInterface $context A controller context object
     * @return  KHttpUrl    A HttpUrl object.
     */
    public function getReferrer(KControllerContextInterface $context)
    {
        $referrer = $this->getObject('koowa:http.url',
            array('url' => $context->request->cookies->get('referrer', 'url'))
        );

        return $referrer;
	}

    /**
     * Set the referrer
     *
     * @param  KControllerContextInterface $context A controller context object
     * @return void
     */
    public function setReferrer(KControllerContextInterface $context)
    {
        if (!$context->request->cookies->has('referrer_locked'))
        {
            $request  = $context->request->getUrl();
            $referrer = $context->request->getReferrer();

            //Compare request url and referrer
            if (!isset($referrer) || ((string)$referrer == (string)$request))
            {
                $controller = $this->getMixer();
                $identifier = $controller->getIdentifier();

                $option   = 'com_' . $identifier->package;
                $view     = KStringInflector::pluralize($identifier->name);
                $referrer = $controller->getView()->getRoute('option=' . $option . '&view=' . $view, true, false);
            }

            //Add the referrer cookie
            $cookie = $this->getObject('koowa:http.cookie', array(
                'name'   => 'referrer',
                'value'  => $referrer,
                'path'   => $this->_cookie_path
            ));

            $context->response->headers->addCookie($cookie);
        }
	}

    /**
     * Lock the referrer from updates
     *
     * @param  KControllerContextInterface  $context A controller context object
     * @return void
     */
    protected function _lockReferrer(KControllerContextInterface $context)
    {
        $cookie = $this->getObject('koowa:http.cookie', array(
            'name'   => 'referrer_locked',
            'value'  => true,
            'path'   => $this->_cookie_path
        ));

        $context->response->headers->addCookie($cookie);
    }

    /**
     * Unlock the referrer for updates
     *
     * @param   KControllerContextInterface  $context A controller context object
     * @return void
     */
    protected function _unlockReferrer(KControllerContextInterface $context)
    {
        $context->response->headers->clearCookie('referrer_locked', $this->_cookie_path);
    }

	/**
	 * Unset the referrer
	 *
     * @param  KControllerContextInterface $context A controller context object
	 * @return void
	 */
	public function _unsetReferrer(KControllerContextInterface $context)
	{
        $context->response->headers->clearCookie('referrer', $this->_cookie_path);
	}

    /**
     * Check if the resource is locked
     *
     * @return bool Returns TRUE if the resource is locked, FALSE otherwise.
     */
    public function isLocked()
    {
        if($this->getModel()->getState()->isUnique())
        {
            $entity = $this->getModel()->getItem();

            if($entity->isLockable() && $entity->isLocked()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the resource is lockable
     *
     * @return bool Returns TRUE if the resource is can be locked, FALSE otherwise.
     */
    public function isLockable()
    {
        $controller = $this->getMixer();

        if($controller instanceof KControllerModellable)
        {
            if($this->getModel()->getState()->isUnique())
            {
                $entity = $this->getModel()->getItem();

                if($entity->isLockable()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Lock the resource
     *
     * Only lock if the context contains a row object and if the user has an active session he can edit or delete the
     * resource. Otherwise don't lock it.
     *
     * @param   KControllerContextInterface  $context A controller context object
     * @return  void
     */
    protected function _lockResource(KControllerContextInterface $context)
    {
        if($this->isLockable() && $this->canEdit()) {
            $context->result->lock();
        }
    }

    /**
     * Unlock the resource
     *
     * @param  KControllerContextInterface  $context A controller context object
     * @return void
     */
    protected function _unlockResource(KControllerContextInterface $context)
    {
        if($this->isLockable() && $this->canEdit()) {
            $context->result->unlock();
        }
    }

    /**
     * Permission handler for save actions
     *
     * Method returns TRUE if the controller implements the ControllerModellable interface.
     *
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canSave()
    {
        if($this->getRequest()->getFormat() == 'html')
        {
            if($this->getModel()->getState()->isUnique())
            {
                if($this->canEdit())
                {
                    if($this->isLockable() && !$this->isLocked()) {
                        return true;
                    }
                }
            }
            else
            {
                if($this->canAdd()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Permission handler for apply actions
     *
     * Method returns TRUE if the controller implements the ControllerModellable interface.
     *
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canApply()
    {
        return $this->canSave();
    }

    /**
     * Permission handler for cancel actions
     *
     * Method returns TRUE if the controller implements the ControllerModellable interface.
     *
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canCancel()
    {
        if($this->getRequest()->getFormat() == 'html') {
            return $this->canRead();
        }

        return false;
    }

	/**
	 * Save action
	 *
	 * This function wraps around the edit or add action. If the model state is unique a edit action will be executed,
     * if not unique an add action will be executed.
	 *
	 * This function also sets the redirect to the referrer.
	 *
	 * @param   KControllerContextInterface $context  A command context object
	 * @return 	KDatabaseRowsetInterface  A row object containing the saved data
	 */
	protected function _actionSave(KControllerContextInterface $context)
	{
        $action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
        $entity = $context->getSubject()->execute($action, $context);

        //Create the redirect
        $context->response->setRedirect($this->getReferrer($context));

        return $entity;
	}

	/**
	 * Apply action
	 *
	 * This function wraps around the edit or add action. If the model state is unique a edit action will be executed,
     * if not unique an add action will be executed.
	 *
	 * This function also sets the redirect to the current url
	 *
	 * @param	KControllerContextInterface $context A command context object
	 * @return 	KDatabaseRowsetInterface 	      A row object containing the saved data
	 */
	protected function _actionApply(KControllerContextInterface $context)
	{
        $action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
        $entity = $context->getSubject()->execute($action, $context);

        //Create the redirect
        $url = $this->getReferrer($context);

        if ($entity instanceof KDatabaseRowInterface)
        {
            $url = clone $context->request->getUrl();

            if ($this->getModel()->getState()->isUnique())
            {
                $states = $this->getModel()->getState()->getValues(true);

                foreach ($states as $key => $value) {
                    $url->query[$key] = $entity->get($key);
                }
            }
            else $url->query[$entity->getIdentityColumn()] = $entity->get($entity->getIdentityColumn());
        }

        $context->response->setRedirect($url);

        return $entity;
	}

	/**
	 * Cancel action
	 *
	 * This function will unlock the row(s) and set the redirect to the referrer
     *
     * @param	KControllerContextInterface $context A command context object
     * @return 	KDatabaseRowInterface 	 A row object containing the saved data
	 */
	protected function _actionCancel(KControllerContextInterface $context)
	{
        //Create the redirect
        $context->response->setRedirect($this->getReferrer($context));

        $entity = $context->getSubject()->execute('read', $context);

        return $entity;
	}

    /**
     * Prevent editing a locked resource
     *
     * If the resource is locked a Retry-After header indicating the time at which the conflicting edits are expected
     * to complete will be added. Clients should wait until at least this time before retrying the request.
     *
     * @param   KControllerContextInterface	$context A controller context object
     * @throws  KControllerExceptionConflict If the resource is locked
     * @return 	void
     */
    protected function _beforeEdit(KControllerContextInterface $context)
    {
        if($this->isLocked())
        {
            $context->response->headers->set('Retry-After', $context->user->getSession()->getLifetime());
            throw new KControllerExceptionConflict('Resource is locked.');
        }
    }

    /**
     * Prevent deleting a locked resource
     *
     * If the resource is locked a Retry-After header indicating the time at which the conflicting edits are expected
     * to complete will be added. Clients should wait until at least this time before retrying the request.
     *
     * @param   KControllerContextInterface	$context A controller context object
     * @throws  KControllerExceptionConflict If the resource is locked
     * @return 	void
     */
    protected function _beforeDelete(KControllerContextInterface $context)
    {
        if($this->isLocked())
        {
            $context->response->headers->set('Retry-After', $context->user->getSession()->getLifetime());
            throw new KControllerExceptionConflict('Resource is locked');
        }
    }
}
