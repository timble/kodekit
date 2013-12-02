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
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
    	$config->append(array(
			'mimetype'	  		=> 'text/html',
    		'template_filters'	=> array('form'),
       	));

    	parent::_initialize($config);
    }

    /**
     * Fetch the view data
     *
     * This function will always fetch the model state. Model data will only be fetched if the auto_fetch property is
     * set to TRUE.
     *
     * @param KViewContext	$context A view context object
     * @return void
     */
    public function fetchData(KViewContext $context)
	{
        $model = $this->getModel();

        //Auto-assign the state to the view
        $context->data->state = $model->getState();

        //Auto-assign the data from the model
        if($this->_auto_fetch)
        {
            //Get the view name
            $name = $this->getName();

            //Assign the data of the model to the view
            if(KStringInflector::isPlural($name))
            {
                $context->data->$name = $model->getList();
                $context->data->total = $model->getTotal();
            }
            else $context->data->$name = $model->getItem();
        }
	}

    /**
     * Force the route to be not fully qualified and escaped
     *
     * @param string|array  $route  The query string used to create the route
     * @param boolean       $fqr    If TRUE create a fully qualified route. Default FALSE.
     * @param boolean       $escape If TRUE escapes the route for xml compliance. Default FALSE.
     * @return KHttpUrl     The route
     */
    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        return parent::getRoute($route, $fqr, $escape);
    }
}
