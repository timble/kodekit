<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Html View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
class KViewHtml extends KViewTemplate
{
	/**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'mimetype'	  		=> 'text/html',
    		'template_filters'	=> array('form'),
       	));

    	parent::_initialize($config);
    }

	/**
	 * Return the views output
	 *
     * This function will always assign the model state to the template. Model data will only be assigned if the
     * auto_assign property is set to TRUE.
 	 *
	 * @return string 	The output of the view
	 */
	public function display()
	{
        $model = $this->getModel();

        //Auto-assign the state to the view
        $this->state = $model->getState();

        //Auto-assign the data from the model
        if($this->_auto_assign)
        {
            //Get the view name
            $name  = $this->getName();

            //Assign the data of the model to the view
            if(KInflector::isPlural($name))
            {
                $this->$name = $model->getList();
                $this->total = $model->getTotal();
            }
            else $this->$name = $model->getItem();
        }

		return parent::display();
	}

    /**
     * Force the route to be not fully qualified and escaped
     *
     * @param string    $route  The query string used to create the route
     * @param boolean   $fqr    If TRUE create a fully qualified route. Default FALSE.
     * @param boolean   $escape If TRUE escapes the route for xml compliance. Default FALSE.
     * @return string The route
     */
    public function createRoute($route = '', $fqr = false, $escape = true)
    {
        return parent::createRoute($route, $fqr, $escape);
    }
}
