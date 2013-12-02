<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Paginator Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperPaginator extends ComKoowaTemplateHelperSelect
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
            $config->limit  = (int) max($config->limit, 1);
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
        }

        parent::_initialize($config);
    }

    /**
     * Render a select box with limit values
     *
     * @param 	array|KObjectConfig 	$config An optional array with configuration options
     * @return 	string	Html select box
     */
    public function limit($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'limit'	  	=> 0,
            'attribs'	=> array(),
        ));

        $html     = '';
        $selected = '';
        $options  = array();

        foreach(array(5, 10, 15, 20, 25, 30, 50, 100) as $value)
        {
            if($value == $config->limit) {
                $selected = $value;
            }

            $options[] = $this->option(array('text' => $value, 'value' => $value));
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
     *
     * @return  string  Html
     */
    public function pagination($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
            'show_limit' => true,
		    'show_count' => true,
            'use_bootstrap' => version_compare(JVERSION, '3.0', '>=') || JFactory::getApplication()->isSite()
        ))->append(array(
            'show_pages' => $config->count !== 1
        ));

        $this->_initialize($config);

        $class = $config->use_bootstrap ? 'pagination-toolbar' : 'pagination-legacy';

        $html = '<div class="pagination '.$class.'">';

        if($config->show_limit) {
            $html .= '<div class="limit">'.$this->limit($config).'</div>';
        }

        if ($config->show_pages)
        {
            if($config->use_bootstrap)
            {
                $html .= '<ul class="pagination-list">';
                $html .=  $this->_bootstrap_pages($this->_items($config));
                $html .= '</ul>';
            } else {
                $html .=  $this->_pages($this->_items($config));
            }
        }

        if($config->show_count) {
            $html .= sprintf($this->translate('JLIB_HTML_PAGE_CURRENT_OF_TOTAL'), $config->current, $config->count);
        }


        $html .= '</div>';

        return $html;
    }

    /**
     * Render a list of pages links
     *
     * This function is overrides the default behavior to render the links in the khepri template backend style.
     *
     * @param   array   $pages An array of page data
     * @return  string  Html
     */
    protected function _pages($pages)
    {
        $class = $pages['first']->active ? '' : 'off';
        $html  = '<div class="button2-right '.$class.'"><div class="start">'.$this->_link($pages['first'], 'Start').'</div></div>';

        $class = $pages['previous']->active ? '' : 'off';
        $html  .= '<div class="button2-right '.$class.'"><div class="prev">'.$this->_link($pages['previous'], 'Prev').'</div></div>';

        $html  .= '<div class="button2-left"><div class="page">';
        foreach($pages['pages'] as $page) {
            $html .= $this->_link($page, $page->page);
        }
        $html .= '</div></div>';

        $class = $pages['next']->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="next">'.$this->_link($pages['next'], 'Next').'</div></div>';

        $class = $pages['last']->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="end">'.$this->_link($pages['last'], 'End').'</div></div>';

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
        $url   = clone KRequest::url();
        $query = $url->getQuery(true);

        //For compatibility with Joomla use limitstart instead of offset
        $query['limit']      = $page->limit;
        $query['limitstart'] = $page->offset;

        $url->setQuery($query);

        $class = $page->current ? 'class="active"' : '';

        if($page->active && !$page->current) {
            $html = '<a href="'.$url.'" '.$class.'>'.$this->translate($title).'</a>';
        } else {
            $html = '<span '.$class.'>'.$this->translate($title).'</span>';
        }

        return $html;
    }

    /**
     * Render a list of page links using Bootstrap HTML output
     *
     * This function is overrides the default behavior to render the links in the khepri template backend style.
     *
     * @param   array   $pages An array of page data
     * @return  string  Html
     */
    protected function _bootstrap_pages($pages)
    {
        $html  = $pages['previous']->active ? '<li>'.$this->_bootstrap_link($pages['previous'], '&larr;').'</li>' : '';

        $padding = 2;
        $current = 0;
        foreach ($pages['pages'] as $i => $page) {
            if($page->current) $current = $i;
        }

        $total = count($pages['pages']);
        $hellip = false;
        foreach ($pages['pages'] as $i => $page)
        {
            $in_range = $i > ($current - $padding) && $i < ($current + $padding);

            if ($i < $padding || $in_range || $i >= ($total - $padding))
            {
                $html .= '<li class="'.($page->active && !$page->current ? '' : 'active').'">';
                $html .= $this->_bootstrap_link($page, $page->page);

                $hellip = false;
            }
            else
            {
                if($hellip == true) continue;

                $html .= '<li class="disabled">';
                $html .= '<a href="#">&hellip;</a>';

                $hellip = true;
            }

            $html .= '</li>';
        }

        $html  .= $pages['next']->active ? '<li>'.$this->_bootstrap_link($pages['next'], '&rarr;').'</li>' : '';

        return $html;
    }

    /**
     * Generates a pagination link using Bootstrap HTML output
     *
     * @param KObject $page Page object
     * @param string  $title Page title
     * @return string
     */
    protected function _bootstrap_link($page, $title)
    {
        $url   = clone KRequest::url();
        $query = $url->getQuery(true);

        //For compatibility with Joomla use limitstart instead of offset
        $query['limit']      = $page->limit;
        $query['limitstart'] = $page->offset;

        $url->setQuery($query);

        if ($page->active && !$page->current) {
            $html = '<a href="'.$url.'">'.$this->translate($title).'</a>';
        } else {
            $html = '<a href="#">'.$this->translate($title).'</a>';
        }

        return $html;
    }

    /**
     * Get a list of pages
     *
     * @param   KObjectConfig $config
     *
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
     *
     * @return  array   Page number => offset
     */
    protected function _offsets(KObjectConfig $config)
    {
        if($display = $config->display)
        {
            $start  = (int) max($config->current - $display, 1);
            $start  = min($config->count, $start);
            $stop   = (int) min($config->current + $display, $config->count);
        }
        else // show all pages
        {
            $start = 1;
            $stop = $config->count;
        }

        $result = array();
        if($start > 0)
        {
            foreach(range($start, $stop) as $pagenumber) {
                $result[$pagenumber] =  ($pagenumber-1) * $config->limit;
            }
        }

        return $result;
    }
}
