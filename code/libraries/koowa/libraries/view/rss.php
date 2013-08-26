<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Rss View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
class KViewRss extends KViewTemplate
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config	An optional KObject object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'layout'   => 'rss',
            'template' => 'rss',
            'mimetype' => 'application/rss+xml',
            'data'     => array(
                'update_period'    => 'hourly',
                'update_frequency' => 1
            )
        ));

        parent::_initialize($config);
    }

	/**
	 * Return the views output
	 *
	 * This function will auto assign the model data to the view if the auto_assign
	 * property is set to TRUE.
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
		    if(KStringInflector::isPlural($name))
			{
		        $this->$name = $model->getList();
				$this->total = $model->getTotal();
		    }
			else $this->$name = $model->getItem();
		}

		return parent::display();
	}

    /**
     * Get the layout to use
     *
     * @return   string The layout name
     */
    public function getLayout()
    {
        return 'default';
    }

    /**
     * Force the route to fully qualified and escaped by default
     *
     * @param   string  $route   The query string used to create the route
     * @param   boolean $fqr     If TRUE create a fully qualified route. Default TRUE.
     * @param   boolean $escape  If TRUE escapes the route for xml compliance. Default FALSE.
     * @return  string  The route
     */
    public function createRoute($route = '', $fqr = true, $escape = true)
    {
        return parent::createRoute($route, $fqr, $escape);
    }
}