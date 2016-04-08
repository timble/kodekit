<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Paginator Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperPaginator extends TemplateHelperSelect
{
    /**
     * Render item pagination
     *
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function pagination($config = array())
    {
        $config = new ModelPaginator($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
            'attribs'    => array(),
            'show_limit' => true,
            'show_count' => true,
            'page_rows'  => array(10, 20, 50, 100)
        ))->append(array(
            'show_pages' => $config->count !== 1
        ));

        $translator = $this->getObject('translator');

        // Do not show pagination when $config->limit is lower then $config->total
        if($config->total > $config->limit)
        {
            $html = '';
            if($config->show_limit) {
                $html .= '<div class="pagination__limit">'.$translator('Display NUM').' '.$this->limit($config).'</div>';
            }

            if($config->show_pages) {
                $html .=  $this->pages($config);
            }

            if($config->show_count) {
                $html .= '<div class="pagination__count"> '.$translator('Page').' '.$config->current.' '.$translator('of').' '.$config->count.'</div>';
            }
            return $html;
        }
        return false;
    }

    /**
     * Render a select box with limit values
     *
     * @param   array|ObjectConfig     $config An optional array with configuration options
     * @return  string  Html select box
     */
    public function limit($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'limit'	    => 0,
            'attribs'   => array(),
            'page_rows' => array(10, 20, 50, 100)
        ));

        $html     = '';
        $selected = 0;
        $options  = array();
        $values   = ObjectConfig::unbox($config->page_rows);

        if ($config->limit && !in_array($config->limit, $values))
        {
            $values[] = $config->limit;
            sort($values);
        }

        foreach($values as $value)
        {
            if($value == $config->limit) {
                $selected = $value;
            }

            $options[] = $this->option(array('label' => $value, 'value' => $value));
        }

        if ($config->limit == $config->total) {
            $options[] = $this->option(array('label' => $this->getObject('translator')->translate('All'), 'value' => 0));
        }

        $html .= $this->optionlist(array('options' => $options, 'name' => 'limit', 'attribs' => $config->attribs, 'selected' => $selected));
        return $html;
    }

    /**
     * Render a list of pages links
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function pages($config = array())
    {
        $config = new ModelPaginator($config);
        $config->append(array(
            'total'    => 0,
            'display'  => 4,
            'offset'   => 0,
            'limit'    => 0,
            'attribs'  => array(),
        ));

        $html = '<ul class="pagination">';
        if($config->offset) {
            $html .= $this->link($config->pages->prev);
        }

        foreach($config->pages->offsets as $offset) {
            $html .= $this->link($offset);
        }

        if($config->total > ($config->offset + $config->limit)) {
            $html .= $this->link($config->pages->next);
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Render a page link
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function link($config)
    {
        $config = new ObjectConfig($config);
        $config->append(array(
            'title'   => '',
            'current' => false,
            'active'  => false,
            'offset'  => 0,
            'limit'   => 0,
            'rel'      => '',
            'attribs'  => array(),
        ));

        $route = $this->getTemplate()->route('limit='.$config->limit.'&offset='.$config->offset);
        $rel   = !empty($config->rel) ? 'rel="'.$config->rel.'"' : '';

        $html = '<li '.$this->buildAttributes($config->attribs).'>';
        $html .= '<a href="'.$route.'" '.$rel.'>'.$this->getObject('translator')->translate($config->title).'</a>';
        $html .= '</li>';

        return $html;
    }
}
