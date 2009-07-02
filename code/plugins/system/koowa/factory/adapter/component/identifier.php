<?php


class KFactoryAdapterComponentIdentifier extends KObject
{
	/**
	 * Application
	 *
	 * @var	string
	 */
	public $application;

	/**
	 * Extension [com|plg|lib|mod]
	 *
	 * @var string
	 */
	public $extension = '';

	/**
	 * Component name
	 *
	 * @var string
	 */
	public $component = '';

	/**
	 * Type name
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Path
	 *
	 * @var array
	 */
	public $path = array();

	/**
	 * Name / suffix
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Constructor
	 *
	 * @param	string	Identifier application::extension.component.type[[.path].name]
	 * @return 	void
	 */
	public function __construct($identifier)
	{
		// we only deal with foo::bar
		if(strpos($identifier, '::') === false) {
			return;
		}

		list($this->application, $parts) = explode('::', $identifier);

		// Admin is an alias for administrator
		$this->application = ($this->application == 'admin') ? 'administrator' : $this->application;


		$parts 			= explode('.', $parts);

		// set the extension
		$this->extension = array_shift($parts);

		// we only deal with components
		if(!$this->extension == 'com') {
			return;
		}

		// Set the component
		$this->component = array_shift($parts);

		// Set the base type
		$this->type		= array_shift($parts);

		// Set the name (last part)
		if(count($parts)) {
			$this->name = array_pop($parts);
		}

		// Set the path (rest)
		if(count($parts)) {
			$this->path = $parts;
		}

	}

	public function __toString()
	{
		$string = $application.'::'.$this->extension.'.'.$this->component.'.'.$this->type;

		if(count($this->path)) {
			$string .= '.'.implode('.',$this->path);
		}

		if(!empty($this->name)) {
			$string .= '.'.$this->name;
		}

		return $string;
	}

	public function getClassName()
	{
		$path =  KInflector::camelize(implode('_', $this->path));

        $classname = ucfirst($this->component).ucfirst($this->type).$path.ucfirst($this->name);
		return $classname;
	}

	public function getBasePath()
	{

		$base_path  = JApplicationHelper::getClientInfo($this->application, true)->path
						.DS.'components'.DS.'com_'.$this->component;

		if(!empty($this->name))
		{
			$base_path .= DS.KInflector::pluralize($this->type);

			if(count($this->path))
			{
				foreach($this->path as $sub) {
					$base_path .= DS.KInflector::pluralize($sub);
				}
			}
		}

		return $base_path;
	}

	/**
	 * Get the filename
	 *
	 * @return string The file name for the class
	 */
	public function getFileName()
	{
		$filename = '';

		switch($this->type)
		{
			case 'view' :
			{
				//Get the document type
				$type   = KFactory::get('lib.joomla.document')->getType();
				$filename = strtolower($this->name).DS.$type.'.php';
			} break;

			default : $filename = strtolower($this->type).'.php';
		}

		return $filename;
	}
}