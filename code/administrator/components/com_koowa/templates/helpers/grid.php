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
class ComDefaultTemplateHelperGrid extends KTemplateHelperGrid
{
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

        if (version_compare(JVERSION, '1.6', '>=')) {
            $class  = $config->row->{$config->field} ? 'publish' : 'unpublish';
            $alt 	= $config->row->{$config->field} ? $this->translate('Published') : $this->translate('Unpublished');
            $text 	= $config->row->{$config->field} ? $this->translate('Unpublish Item') : $this->translate('Publish Item');
    
            $config->data->{$config->field} = $config->row->{$config->field} ? 0 : 1;
            $data = str_replace('"', '&quot;', $config->data);
    
            $html = '<a class="jgrid" href="#" data-action="edit" data-data="'.$data.'" title="'.$text.'">';
            $html .= '<span class="state '.$class.'"><span class="text">'.$alt.'</span></span></a>';
        } else {
            $img    = $config->row->{$config->field} ? 'enabled.png' : 'disabled.png';
            $alt 	= $config->row->{$config->field} ? $this->translate( 'Published' ) : $this->translate( 'Unpublished' );
            $text 	= $config->row->{$config->field} ? $this->translate( 'Unpublish Item' ) : $this->translate( 'Publish Item' );
            
            $config->data->{$config->field} = $config->row->{$config->field} ? 0 : 1;
            $data = str_replace('"', '&quot;', $config->data);
            
            $html = '<img src="media://lib_koowa/images/'. $img .'" border="0" alt="'. $alt .'" data-action="edit" data-data="'.$data.'" title='.$text.' />';
            
        }

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

        if (version_compare(JVERSION, '1.6', '>=')) {
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
        } else {
            $html = parent::order($config);
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

        if (version_compare(JVERSION, '1.6', '>=')) {
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
        } else {
            $html = parent::access();
        }

        return $html;
    }
}
