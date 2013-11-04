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

        if ($this->isDispatched() && KRequest::type() == 'HTTP' && $this->getRequest()->query->format === 'html')
        {
            $this->registerCallback('before.read' , array($this, 'setReferrer'));
            $this->registerCallback('after.apply' , array($this, 'lockReferrer'));
			$this->registerCallback('after.read'  , array($this, 'unlockReferrer'));
	        $this->registerCallback('after.save'  , array($this, 'unsetReferrer'));
			$this->registerCallback('after.cancel', array($this, 'unsetReferrer'));
        }

		$this->registerCallback('after.read'  , array($this, 'lockResource'));
		$this->registerCallback('after.save'  , array($this, 'unlockResource'));
		$this->registerCallback('after.cancel', array($this, 'unlockResource'));

		//Set the default redirect.
        $this->setRedirect(KRequest::referrer());

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
            'cookie_path' => KRequest::base().'/'
        ));

        parent::_initialize($config);
    }

	/**
	 * Lock the referrer from updates
	 *
	 * @return void
	 */
	public function lockReferrer()
	{
        setcookie('referrer_locked', 1, 0, $this->_cookie_path);
	}

	/**
	 * Unlock the referrer for updates
	 *
	 * @return void
	 */
	public function unlockReferrer()
	{
        setcookie('referrer_locked', null, 0, $this->_cookie_path);
	}


	/**
	 * Get the referrer
	 *
	 * @return KHttpUrl	A KHttpUrl object.
	 */
	public function getReferrer()
	{
        $referrer = KRequest::get('cookie.referrer', 'url');

        if ($referrer)
        {
            $referrer = $this->getObject('koowa:http.url',
                array('url' => $referrer)
            );
        }

	    return $referrer;
	}

	/**
	 * Set the referrer
	 *
	 * @return void
	 */
	public function setReferrer()
	{
	    if(!KRequest::has('cookie.referrer_locked'))
	    {
	        $request  = KRequest::url();
	        $referrer = KRequest::referrer();

	        //Compare request url and referrer
	        if(!isset($referrer) || ((string) $referrer == (string) $request))
	        {
                $identifier = $this->getMixer()->getIdentifier();
	            $option     = 'com_'.$identifier->package;
	            $view       = KStringInflector::pluralize($identifier->name);
	            $url        = 'index.php?option='.$option.'&view='.$view;

	            $referrer = $this->getObject('koowa:http.url',array('url' => $url));

	        }

            setcookie('referrer', (string) $referrer, 0, $this->_cookie_path);
		}
	}

	/**
	 * Unset the referrer
	 *
	 * @return void
	 */
	public function unsetReferrer()
	{
        setcookie('referrer', null, 0, $this->_cookie_path);
	}

	/**
	 * Lock callback
	 *
	 * Only lock if the context contains a row object and the view layout is 'form'.
	 *
	 * @param  KControllerContextInterface $context The active command context
	 * @return void
	 */
	public function lockResource(KControllerContextInterface $context)
	{
       if($context->result instanceof KDatabaseRowInterface)
       {
	        $view = $this->getView();

	        if($view instanceof KViewTemplate)
	        {
                if($view->getLayout() == 'form' && $context->result->isLockable()) {
		            $context->result->lock();
		        }
            }
	    }
	}

	/**
	 * Unlock callback
	 *
	 * @param 	KControllerContextInterface $context The active command context
	 * @return void
	 */
	public function unlockResource(KControllerContextInterface $context)
	{
	    if($context->result instanceof KDatabaseRowInterface && $context->result->isLockable()) {
			$context->result->unlock();
		}
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
		$data   = $context->subject->execute($action, $context);

		//Create the redirect
		$this->setRedirect($this->getReferrer());

		return $data;
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
		$data   = $context->subject->execute($action, $context);

		//Create the redirect
		$url = $this->getReferrer();

		if ($data instanceof KDatabaseRowInterface)
		{
            $url = clone KRequest::url();

		    if($this->getModel()->getState()->isUnique())
		    {
	            $states = $this->getModel()->getState()->getValues(true);

		        foreach($states as $key => $value) {
		            $url->query[$key] = $data->$key;
		        }
		    }
		    elseif ($data->getIdentityColumn()) {
                $column = $data->getIdentityColumn();
                $url->query[$column] = $data->$column;
            }
        }

		$this->setRedirect($url);

		return $data;
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
		$this->setRedirect($this->getReferrer());

	    $data = $context->subject->execute('read', $context);

		return $data;
	}
}
