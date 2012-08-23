<?php
/**
 * @version		$Id$
 * @package		Koowa_Text
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Text Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_View
 * @uses		KMixinClass
 * @uses 		KTemplate
 */
class KText extends KObject implements KServiceInstantiatable
{
    protected $_translator;
    
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // KConfig::unbox is needed since an array could be passed for a static method
        $this->setTranslator(KConfig::unbox($config->translator));
    }
    
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'translator' => null
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KDispatcherDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
    
            //Add the factory map to allow easy access to the singleton
            $container->setAlias('text', $config->service_identifier);
        }
    
        return $container->get($config->service_identifier);
    }
    
    /**
     * Set the translator for the class
     * 
     * @param mixed $translator
     * @throws KTextException
     * @return KText
     */
    public function setTranslator($translator)
    {
        if (is_object($translator) || is_callable($translator)) {
            $this->_translator = $translator;
        } else {
            throw new KTextException('Invalid translator');
        }
        
        return $this;
    }
    
    public function getTranslator()
    {
        return $this->_translator;
    }
    
    /**
     * Calls the translator object to translate a key
     * 
     * @param string $key
     */
    public function translate($key)
    {
        if (is_object($this->_translator)) {
            return $this->_translator->translate($key);
        } elseif (is_callable($this->_translator)) {
            return call_user_func($this->_translator, $key);
        }
    }
    
    /**
     * Shortcut for translate method
     * @param string $key
     */
    public static function _($key)
    {
        return KService::get('koowa:text')->translate($key);
    }
}