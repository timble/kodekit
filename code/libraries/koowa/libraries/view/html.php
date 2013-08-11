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
	 * This function will auto assign the model data to the view if the auto_assign
	 * property is set to TRUE.
 	 *
	 * @return string 	The output of the view
	 */
	public function display()
	{
	    if(empty($this->output))
		{
	        $model = $this->getModel();

		    //Auto-assign the state to the view
		    $this->assign('state', $model->getState());

		    //Auto-assign the data from the model
		    if($this->_auto_assign)
		    {
			    //Get the view name
			    $name  = $this->getName();

			    //Assign the data of the model to the view
			    if(KInflector::isPlural($name))
			    {
				    $this->assign($name, 	$model->getList())
					     ->assign('total',	$model->getTotal());
			    }
			    else $this->assign($name, $model->getItem());
		    }
		}

		return parent::display();
	}
}
