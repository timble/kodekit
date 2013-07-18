<?php
/**
 * @version		$Id$
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Grid Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 * @see 		http://ajaxpatterns.org/Data_Grid
 */
class KTemplateHelperGrid extends KTemplateHelperAbstract
{
	/**
	 * Render a checkbox field
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function checkbox($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'row'  		=> null,
		));

		if($config->row->isLockable() && $config->row->locked())
		{
		    $html = '<span class="editlinktip hasTip" title="'.$config->row->lockMessage() .'">
						<img src="media://koowa/library/images/locked.png"/>
					</span>';
		}
		else
		{
		    $column = $config->row->getIdentityColumn();
		    $value  = $config->row->{$column};

		    $html = '<input type="checkbox" class="-koowa-grid-checkbox" name="'.$column.'[]" value="'.$value.'" />';
		}

		return $html;
	}

	/**
	 * Render an search header
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function search($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'search' => null
		));

	    $html = '<input name="search" id="search" value="'.$this->getTemplate()->getView()->escape($config->search).'" />';
        $html .= '<button>'.$this->translate('Go').'</button>';
		$html .= '<button onclick="document.getElementById(\'search\').value=\'\';this.form.submit();">'.$this->translate('Reset').'</button>';

	    return $html;
	}

	/**
	 * Render a checkall header
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function checkall($config = array())
	{
		$config = new KConfig($config);

		$html = '<input type="checkbox" class="-koowa-grid-checkall" />';
		return $html;
	}

	/**
	 * Render a sorting header
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function sort( $config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'title'   	=> '',
			'column'  	=> '',
			'direction' => 'asc',
			'sort'		=> ''
		));


		//Set the title
		if(empty($config->title)) {
			$config->title = ucfirst($config->column);
		}

		//Set the direction
		$direction	= strtolower($config->direction);
		$direction 	= in_array($direction, array('asc', 'desc')) ? $direction : 'asc';

		//Set the class
		$class = '';
		if($config->column == $config->sort)
		{
			$direction = $direction == 'desc' ? 'asc' : 'desc'; // toggle
			$class = 'class="-koowa-'.$direction.'"';
		}

		$url = clone KRequest::url();

		$query 				= $url->getQuery(1);
		$query['sort'] 		= $config->column;
		$query['direction'] = $direction;
		$url->setQuery($query);

		$html  = '<a href="'.$url.'" title="'.$this->translate('Click to sort by this column').'"  '.$class.'>';
		$html .= $this->translate($config->title);
		$html .= '</a>';

		// Mark the current column
        if ($config->column == $config->sort) {
            $icon = 'sort_'.(strtolower($config->direction) === 'asc' ? 'asc' : 'desc');
            $html .= ' <img src="media://system/images/'.$icon.'.png">';
        }

		return $html;
	}

	/**
	 * Render an enable field
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function enable($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'row'  		=> null,
		    'field'		=> 'enabled'
		))->append(array(
		    'data'		=> array($config->field => $config->row->{$config->field})
		));

		$img    = $config->row->{$config->field} ? 'enabled.png' : 'disabled.png';
		$alt 	= $config->row->{$config->field} ? $this->translate( 'Enabled' ) : $this->translate( 'Disabled' );
		$text 	= $config->row->{$config->field} ? $this->translate( 'Disable Item' ) : $this->translate( 'Enable Item' );

	    $config->data->{$config->field} = $config->row->{$config->field} ? 0 : 1;
	    $data = str_replace('"', '&quot;', $config->data);

		$html = '<img src="media://koowa/library/images/'. $img .'" border="0" alt="'. $alt .'" data-action="edit" data-data="'.$data.'" title='.$text.' />';

		return $html;
	}
}