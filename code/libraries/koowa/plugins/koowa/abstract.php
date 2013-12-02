<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Default Plugin
 *
 * Koowa plugins can handle a number of events that are dynamically generated. The following is a list of available
 * events. This list is not meant to be exclusive.
 *
 * onBeforeController[Action]
 * onAfterController[Action]
 * where [Action] is Browse, Read, Edit, Add, Delete or any custom controller action
 *
 * onBeforeDatabase[Action]
 * onAfterDatabase[Action]
 * where [Action] is Select, Insert, Update or Delete
 *
 * You can create your own Koowa plugins very easily :
 *
 * <code>
 * <?php
 *  class plgKoowaFoo extends PlgKoowaAbstract
 * {
 *      public function onBeforeControllerBrowse(KEvent $event)
 *      {
 *          //The caller is a reference to the object that is triggering this event
 *          $caller = $event->subject;
 *
 *          //The result is the actual result of the event, if this is an after event
 *          //the result will contain the result of the action.
 *          $result = $event->result;
 *
 *          //The context object can also contain a number of custom properties
 *          print_r($context);
 *      }
 * }
 * </code>
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Plugin\Koowa
 */
abstract class PlgKoowaAbstract extends KEventListener
{
	/**
	 * A JRegistry object holding the parameters for the plugin
	 *
	 * @var	JRegistry
	 */
	protected $_params	= null;

	/**
	 * The name of the plugin
	 *
	 * @var		string
	 */
	protected $_name = null;

	/**
	 * The plugin type
	 *
	 * @var		string
	 */
	protected $_type = null;

    /**
     * Constructor.
     *
     * @param   object          $dispatcher Event dispatcher
     * @param   array|KObjectConfig   $config     Configuration options
     */
	function __construct($dispatcher, $config = array())
	{
		if (isset($config['params']))
		{
		    if ($config['params'] instanceof JRegistry) {
				$this->_params = $config['params'];
			} else {
				$this->_params = new JRegistry;
                $this->_params->loadString($config['params'], 'INI');
			}
		}

		if ( isset( $config['name'] ) ) {
			$this->_name = $config['name'];
		}

		if ( isset( $config['type'] ) ) {
			$this->_type = $config['type'];
		}

		//Inject the identifier
		$config['object_identifier'] = KObjectManager::getInstance()->getIdentifier('plg:koowa.'.$this->_name);

		//Inject the object manager
		$config['object_manager'] = KObjectManager::getInstance();

		//Inject the dispatcher
		$config['dispatcher'] = KObjectManager::getInstance()->getObject('event.dispatcher');

		parent::__construct(new KObjectConfig($config));
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param	string 	$extension 	The extension for which a language file should be loaded
	 * @param	string 	$basePath  	The basepath to use
	 * @return	boolean	True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = JPATH_BASE)
	{
		if(empty($extension)) {
			$extension = 'plg_'.$this->_type.'_'.$this->_name;
		}

		return JFactory::getLanguage()->load( strtolower($extension), $basePath);
	}
}
