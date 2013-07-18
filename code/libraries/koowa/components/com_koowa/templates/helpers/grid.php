<?php
/**
 * @version     $Id$
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Grid Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaTemplateHelperGrid extends KTemplateHelperAbstract
{
    /**
     * Render a checkbox field
     *
     * @param 	array 	$config An optional array with configuration options
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
						<img src="media://koowa/com_koowa/images/locked.png"/>
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
     * @param 	array 	$config An optional array with configuration options
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
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function checkall($config = array())
    {
        $html = '<input type="checkbox" class="-koowa-grid-checkall" />';
        return $html;
    }

    /**
     * Render a sorting header
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function sort($config = array())
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
     * TODO: might want to take this out
     *
     * @param 	array 	$config An optional array with configuration options
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

        $html = '<img src="media://koowa/com_koowa/images/'. $img .'" border="0" alt="'. $alt .'" data-action="edit" data-data="'.$data.'" title='.$text.' />';

        return $html;
    }

    /**
     * Render a publish field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function publish($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'row'  		=> null,
            'field'		=> 'enabled',
            'clickable'  => true
        ))->append(array(
            'data'		=> array($config->field => $config->row->{$config->field})
        ));

        $class  = $config->row->{$config->field} ? 'publish' : 'unpublish';
        $alt 	= $config->row->{$config->field} ? $this->translate('Published') : $this->translate('Unpublished');
        $text 	= $config->row->{$config->field} ? $this->translate('Unpublish Item') : $this->translate('Publish Item');

        $config->data->{$config->field} = $config->row->{$config->field} ? 0 : 1;
        $data = str_replace('"', '&quot;', $config->data);

        $html = '<a class="jgrid" href="#" data-action="edit" data-data="'.$data.'" title="'.$text.'">';
        $html .= '<span class="state '.$class.'"><span class="text">'.$alt.'</span></span></a>';

        return $html;
    }

    public function order($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'row'  		=> null,
            'total'		=> null,
            'field'		=> 'ordering',
            'data'		=> array('order' => 0)
        ));

        $html = '';

        $config->data->order = -1;
        $updata   = str_replace('"', '&quot;', $config->data);

        $config->data->order = +1;
        $downdata = str_replace('"', '&quot;', $config->data);

        $tmpl = '
            <span>
                <a class="jgrid" href="#" title="%s" data-action="edit" data-data="%s">
                    <span class="state %s" style="background-repeat: no-repeat"><span class="text">%s</span></span>
                </a>
            </span>
            ';

        if ($config->row->{$config->field} > 1) {
            $icon = version_compare(JVERSION, '3.0', '>=') ? '<i class="icon-arrow-up"></i>' : $this->translate('Move up');
            $html .= sprintf($tmpl, $this->translate('Move up'), $updata, 'uparrow', $icon);
        }

        $html .= $config->row->{$config->field};

        if ($config->row->{$config->field} != $config->total) {
            $icon = version_compare(JVERSION, '3.0', '>=') ? '<i class="icon-arrow-down"></i>' : $this->translate('Move down');
            $html .= sprintf($tmpl, $this->translate('Move down'), $downdata, 'downarrow', $icon);
        }

        return $html;
    }

    public function access($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'row'  		=> null,
            'field'		=> 'access'
        ));

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.title AS text');
        $query->from('#__viewlevels AS a');
        $query->where('id = '.(int) $config->row->{$config->field});
        $query->group('a.id, a.title, a.ordering');
        $query->order('a.ordering ASC');
        $query->order($query->qn('title') . ' ASC');

        // Get the options.
        $db->setQuery($query);
        $html = $db->loadResult();

        return $html;
    }
}
