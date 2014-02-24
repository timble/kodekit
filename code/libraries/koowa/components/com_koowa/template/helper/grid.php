<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Grid Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperGrid extends KTemplateHelperAbstract
{
    /**
     * Render a radio field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function radio($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'row'  		=> null,
            'attribs' => array()
        ));

        if($config->row->isLockable() && $config->row->locked())
        {
            $html = $this->getTemplate()->renderHelper('behavior.tooltip');
            $html .= '<span class="koowa-tooltip koowa_icon--locked"
                           title="'.$this->getTemplate()->renderHelper('grid.lock_message', array('row' => $config->row)).'">
					</span>';
        }
        else
        {
            $column = $config->row->getIdentityColumn();
            $value  = $config->row->{$column};

            $attribs = $this->buildAttributes($config->attribs);

            $html = '<input type="radio" class="-koowa-grid-checkbox" name="%s[]" value="%s" %s />';
            $html = sprintf($html, $column, $value, $attribs);
        }

        return $html;
    }
    /**
     * Render a checkbox field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function checkbox($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'row'  		=> null,
            'attribs' => array()
        ));

        if($config->row->isLockable() && $config->row->locked())
        {
            $html = $this->getTemplate()->renderHelper('behavior.tooltip');
            $html .= '<span class="koowa-tooltip koowa_icon--locked"
                           title="'.$this->getTemplate()->renderHelper('grid.lock_message', array('row' => $config->row)).'">
					</span>';
        }
        else
        {
            $column = $config->row->getIdentityColumn();
            $value  = $config->row->{$column};

            $attribs = $this->buildAttributes($config->attribs);


            $html = '<input type="checkbox" class="-koowa-grid-checkbox" name="%s[]" value="%s" %s />';
            $html = sprintf($html, $column, $value, $attribs);
        }

        return $html;
    }

    /**
     * Render a search box
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function search($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'search'      => null,
            'placeholder' => $this->translate('Find by title or description&hellip;')
        ));

        $html  = '<label for="search"><i class="icon-search"></i></label>';
        $html .= '<input type="search" name="search" id="search" placeholder="'.$config->placeholder.'" value="'.$this->escape($config->search).'" />';

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
        $config = new KObjectConfigJson($config);
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

        $url = clone $this->getTemplate()->getView()->getUrl();

        $query 				= $url->getQuery(1);
        $query['sort'] 		= $config->column;
        $query['direction'] = $direction;
        $url->setQuery($query);

        $html  = '<a href="'.$url.'" title="'.$this->translate('Click to sort by this column').'"  '.$class.'>';
        $html .= $this->translate($config->title);

        // Mark the current column
        if ($config->column == $config->sort) {
            if (strtolower($config->direction) === 'asc') {
                $html .= ' <span class="koowa_icon--sort_up koowa_icon--12"></span>';
            } else {
                $html .= ' <span class="koowa_icon--sort_down koowa_icon--12"></span>';
            }
        }
        else {
            $html .= ' <span class="koowa_icon--sort koowa_icon--12"></span>';
        }

        $html .= '</a>';

        return $html;
    }

    /**
     * Render an enable field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function enable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'row'  		=> null,
            'field'		=> 'enabled',
            'clickable' => true
        ))->append(array(
            'enabled'   => (bool) $config->row->{$config->field},
            'data'		=> array($config->field => $config->row->{$config->field} ? 0 : 1),
        ))->append(array(
            'alt'       => $config->enabled ? $this->translate('Enabled') : $this->translate('Disabled'),
            'tooltip'   => $config->enabled ? $this->translate('Disable Item') : $this->translate('Enable Item'),
            'color'     => $config->enabled ? '#468847' : '#b94a48',
            'icon'      => $config->enabled ? 'enabled' : 'disabled',
        ));

        if ($config->clickable)
        {
            $data    = htmlentities(json_encode($config->data->toArray()));
            $attribs = 'style="cursor: pointer;color:'.$config->color.'" data-action="edit" data-data="'.$data.'"
                title="'.$config->tooltip.'"';
        } else {
            $attribs = 'style="color:'.$config->color.'"';
        }

        $html = '<span class="koowa-tooltip koowa_icon--%s" %s><i>%s</i></span>';
        $html = sprintf($html, $config->icon, $attribs, $config->alt);
        $html .= $this->getTemplate()->renderHelper('behavior.tooltip');

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
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'row'  		=> null,
            'field'		=> 'enabled',
            'clickable' => true
        ))->append(array(
            'enabled'   => (bool) $config->row->{$config->field},
        ))->append(array(
            'alt'       => $config->enabled ? $this->translate('Published') : $this->translate('Unpublished'),
            'tooltip'   => $config->enabled ? $this->translate('Unpublish Item') : $this->translate('Publish Item'),
            'color'     => $config->enabled ? '#468847' : '#b94a48',
            'icon'      => $config->enabled ? 'enabled' : 'disabled',
        ));

        return $this->enable($config);
    }

    /**
     * Render an order field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function order($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'row'  		=> null,
            'total'		=> null,
            'field'		=> 'ordering',
            'data'		=> array('order' => 0)
        ));

        $html = '';

        $config->data->order = -1;

        $updata = $config->data->toArray();
        $updata = htmlentities(json_encode($updata));

        $config->data->order = +1;

        $downdata = $config->data->toArray();
        $downdata = htmlentities(json_encode($downdata));

        if ($config->sort === $config->field)
        {
            $tmpl = '
                <span>
                    <a class="jgrid" href="#" title="%s" data-action="edit" data-data="%s">
                        <span class="state %s" style="width: 12px; height: 12px; background-repeat: no-repeat"><span class="text">%s</span></span>
                    </a>
                </span>
                ';
        }
        else
        {
            $tmpl = '
                <span class="jgrid">
                    <span class="state %3$s" style="width: 12px; height: 12px; background-repeat: no-repeat; background-position: 0 -12px;">
                        <span class="text">%4$s</span>
                    </span>
                </span>';
        }

        if ($config->row->{$config->field} > 1) {
            $icon = version_compare(JVERSION, '3.0', '>=') ? '<i class="icon-arrow-up"></i>' : $this->translate('Move up');
            $html .= sprintf($tmpl, $this->translate('Move up'), $updata, 'uparrow', $icon);
        }

        $html .= $config->row->{$config->field};

        if ($config->row->{$config->field} != $config->total) {
            $icon = version_compare(JVERSION, '3.0', '>=') ? '<i class="icon-arrow-down"></i>' : $this->translate('Move down');
            $html .= sprintf($tmpl, $this->translate('Move down'), $downdata, 'downarrow', $icon);
        }

        if ($config->sort !== $config->field)
        {
            $html = '<div class="koowa-tooltip"
                          data-koowa-tooltip="'.htmlentities(json_encode(array('placement' => 'left'))).'"
                          title="'.$this->translate('Please order by this column first by clicking the column title').'">'
                    .$html.
                    '</div>';
        }


        return $html;
    }

    /**
     * Render an access field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function access($config = array())
    {
        $config = new KObjectConfigJson($config);
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

    /**
     * Get the locked information
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @throws UnexpectedValueException
     * @return string The locked by "name" "date" message
     */
    public function lock_message($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'row' => null
        ));

        if (!($config->row instanceof KDatabaseRowInterface)) {
            throw new UnexpectedValueException('$config->row should be a KDatabaseRowInterface instance');
        }

        $row = $config->row;
        $message = '';

        if($row->isLockable() && $row->locked())
        {
            $user = $this->getObject('user.provider')->load($row->locked_by);
            $date = $this->getObject('date', array('date' => $row->locked_on));

            $message = $this->getObject('translator')->translate(
                'Locked by {name} {date}', array('name' => $user->getName(), 'date' => $date->humanize())
            );
        }

        return $message;
    }
}
