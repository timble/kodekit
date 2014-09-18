<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Grid Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperGrid extends KTemplateHelperGrid
{
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
            'entity'    => null,
            'total'     => null,
            'field'     => 'ordering',
            'data'      => array('order' => 0)
        ));

        $translator = $this->getObject('translator');

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

        if ($config->entity->{$config->field} > 1)
        {
            $icon = version_compare(JVERSION, '3.0', '>=') ? '<i class="icon-arrow-up"></i>' : $translator->translate('Move up');
            $html .= sprintf($tmpl, $translator->translate('Move up'), $updata, 'uparrow', $icon);
        }

        $html .= $config->entity->{$config->field};

        if ($config->entity->{$config->field} != $config->total)
        {
            $icon = version_compare(JVERSION, '3.0', '>=') ? '<i class="icon-arrow-down"></i>' : $translator->translate('Move down');
            $html .= sprintf($tmpl, $translator->translate('Move down'), $downdata, 'downarrow', $icon);
        }

        if ($config->sort !== $config->field)
        {
            $html = '<div class="koowa-tooltip"
                          data-koowa-tooltip="'.htmlentities(json_encode(array('placement' => 'left'))).'"
                          title="'.$translator->translate('Please order by this column first by clicking the column title').'">'
                    .$html.
                    '</div>';
        }


        return $html;
    }

    /**
     * Render an access field
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function access($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity'  		=> null,
            'field'		=> 'access'
        ));

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.title AS text');
        $query->from('#__viewlevels AS a');
        $query->where('id = '.(int) $config->entity->{$config->field});
        $query->group('a.id, a.title, a.ordering');
        $query->order('a.ordering ASC');
        $query->order($query->qn('title') . ' ASC');

        // Get the options.
        $db->setQuery($query);
        $html = $db->loadResult();

        return $html;
    }
}
