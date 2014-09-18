<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Paginator Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Helper
 */
class KTemplateHelperPaginator extends KTemplateHelperSelect
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        if($config->total != 0)
        {
            $config->limit  = (int) max($config->limit, 0);
            $config->offset = (int) max($config->offset, 0);

            if($config->limit > $config->total) {
                $config->offset = 0;
            }

            if(!$config->limit)
            {
                $config->offset = 0;
                $config->limit  = $config->total;
            }

            $config->count  = (int) ceil($config->total / $config->limit);

            if($config->offset > $config->total) {
                $config->offset = ($config->count-1) * $config->limit;
            }

            $config->current = (int) floor($config->offset / $config->limit) + 1;
        }
        else
        {
            $config->limit   = 0;
            $config->offset  = 0;
            $config->count   = 0;
            $config->current = 0;

            $config->show_pages = false;
        }

        if ($config->count === 1) {
            $config->show_pages = false;
        }

        parent::_initialize($config);
    }

    /**
     * Render a select box with limit values
     *
     * @param   array|KObjectConfig     $config An optional array with configuration options
     * @return  string  Html select box
     */
    public function limit($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'limit'	  => 0,
            'attribs' => array(),
            'values'  => array(5, 10, 15, 20, 25, 30, 50, 100)
        ));

        $html     = '';
        $selected = 0;
        $options  = array();
        $values   = KObjectConfig::unbox($config->values);

        if ($config->limit && !in_array($config->limit, $values)) {
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
     * Render item pagination
     *
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function pagination($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 2,
            'offset'     => 0,
            'limit'      => 0,
            'show_limit' => true,
            'show_count' => false
        ))->append(array(
            'show_pages' => $config->count !== 1
        ));

        $this->_initialize($config);

        $html = '<div class="pagination pagination-toolbar">';

        if($config->show_limit) {
            $html .= '<div class="limit">'.$this->limit($config).'</div>';
        }

        if ($config->show_pages)
        {
            $html .= '<ul class="pagination-list">';
            $html .=  $this->_pages($this->_items($config));
            $html .= '</ul>';
        }

        if($config->show_count) {
            $html .= sprintf($this->getObject('translator')->translate('Page %s of %s'), $config->current, $config->count);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render a list of page links
     *
     * @param   array   $pages An array of page data
     * @return  string  Html
     */
    protected function _pages($pages)
    {
        $html = '';

        //$html .= $pages['first']->active ? '<li>'.$this->_link($pages['first'], '<i class="icon-fast-backward icon-first"></i>').'</li>' : '';

        $html .= $pages['previous']->active ? '<li>'.$this->_link($pages['previous'], '&laquo;').'</li>' : '';

        $previous = null;
        foreach ($pages['pages'] as $page)
        {
            if ($previous && $page->page - $previous->page > 1) {
                $html .= '<li class="disabled"><a>&hellip;</a></li>';
            }

            $html .= '<li class="'.($page->active && !$page->current ? '' : 'active').'">';
            $html .= $this->_link($page, $page->page);
            $html .= '</li>';

            $previous = $page;
        }

        $html  .= $pages['next']->active ? '<li>'.$this->_link($pages['next'], '&raquo;').'</li>' : '';

        //$html  .= $pages['last']->active ? '<li>'.$this->_link($pages['last'], '<i class="icon-fast-forward icon-last"></i>').'</li>' : '';

        return $html;
    }

    /**
     * Generates a pagination link
     *
     * @param KObject $page Page object
     * @param string  $title Page title
     * @return string
     */
    protected function _link($page, $title)
    {
        $url   = $this->getObject('request')->getUrl();
        $query = $url->getQuery(true);

        //For compatibility with Joomla use limitstart instead of offset
        $query['limit']      = $page->limit;
        $query['limitstart'] = $page->offset;

        unset($query['offset']);

        $url->setQuery($query);

        if ($page->active && !$page->current) {
            $html = '<a href="'.$url.'">'.$this->getObject('translator')->translate($title).'</a>';
        } else {
            $html = '<a>'.$this->getObject('translator')->translate($title).'</a>';
        }

        return $html;
    }

    /**
     * Get a list of pages
     *
     * @param   KObjectConfig $config
     * @return  array   Returns and array of pages information
     */
    protected function _items(KObjectConfig $config)
    {
        $elements  = array();

        // First
        $offset  = 0;
        $active  = $offset != $config->offset;
        $props   = array('page' => 1, 'offset' => $offset, 'limit' => $config->limit, 'current' => false, 'active' => $active );

        $elements['first'] = (object) $props;

        // Previous
        $offset  = max(0, ($config->current - 2) * $config->limit);
        $active  = $offset != $config->offset;
        $props   = array('page' => $config->current - 1, 'offset' => $offset, 'limit' => $config->limit, 'current' => false, 'active' => $active);
        $elements['previous'] = (object) $props;

        // Pages
        $elements['pages'] = array();
        foreach($this->_offsets($config) as $page => $offset)
        {
            $current = $offset == $config->offset;
            $props = array('page' => $page, 'offset' => $offset, 'limit' => $config->limit, 'current' => $current, 'active' => !$current);
            $elements['pages'][] = (object) $props;
        }

        // Next
        $offset  = min(($config->count-1) * $config->limit, ($config->current) * $config->limit);
        $active  = $offset != $config->offset;
        $props   = array('page' => $config->current + 1, 'offset' => $offset, 'limit' => $config->limit, 'current' => false, 'active' => $active);
        $elements['next'] = (object) $props;

        // Last
        $offset  = ($config->count - 1) * $config->limit;
        $active  = $offset != $config->offset;
        $props   = array('page' => $config->count, 'offset' => $offset, 'limit' => $config->limit, 'current' => false, 'active' => $active);
        $elements['last'] = (object) $props;

        return $elements;
    }

    /**
     * Get the offset for each page, optionally with a range
     *
     * @param   KObjectConfig $config
     * @return  array   Page number => offset
     */
    protected function _offsets(KObjectConfig $config)
    {
        if($display = $config->display)
        {
            $start  = min($config->count, (int) max($config->current - $display, 1));
            $stop   = (int) min($config->current + $display, $config->count);

            $pages = range($start, $stop);

            if ($config->current > 2) {
                array_unshift($pages, 1, 2);
            }

            if ($config->count - $config->current > 2) {
                array_push($pages, $config->count-1, $config->count);
            }
        }
        else $pages = range(1, $config->count);

        $result = array();
        foreach($pages as $pagenumber) {
            $result[$pagenumber] =  ($pagenumber-1) * $config->limit;
        }

        return $result;
    }
}
