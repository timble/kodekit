<?php
/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Paginator Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 * @uses        KRequest
 * @uses        KConfig
 */
class ComDefaultTemplateHelperPaginator extends KTemplateHelperPaginator
{
    /**
     * Render item pagination
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     */
    public function pagination($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
            'show_limit' => true,
		    'show_count' => true
        ));

        $this->_initialize($config);
        
        $j15 = version_compare(JVERSION, '1.6', '<');
        $j30 = version_compare(JVERSION, '3.0', '>=');
        
        $html = '';

        if ($j15) {
            $html .= '<div class="container">';
        }


        if ($j30) {
            $html .= '<div class="pagination pagination-toolbar">';
        } else {
            $html .= '<div class="pagination pagination-legacy">';
        }
        if($config->show_limit) {
            $html .= '<div class="limit">'.$this->translate('Display NUM').' '.$this->limit($config).'</div>';
        }
        if($j30) {
            $html .= '<ul class="pagination-list">';
            $html .=  $this->_bootstrap_pages($this->_items($config));
            $html .= '</ul>';
        } else {
            $html .=  $this->_pages($this->_items($config));
            if($config->show_count) {
                if ($j15) {
                    $html .= '<div class="limit"> '.$this->translate('Page').' '.$config->current.' '.$this->translate('of').' '.$config->count.'</div>';
                } else {
                    $html .= sprintf($this->translate('JLIB_HTML_PAGE_CURRENT_OF_TOTAL'), $config->current, $config->count);
                }
            }
        }
        $html .= '</div>';
        
        if ($j15) {
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Render a list of pages links
     *
     * This function is overriddes the default behavior to render the links in the khepri template
     * backend style.
     *
     * @param   araay   An array of page data
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

    protected function _bootstrap_pages($pages)
    {
        $html  = $pages['previous']->active ? '<li>'.$this->_bootstrap_link($pages['previous'], '&larr;').'</li>' : '';

        /* @TODO should be a better way to do this than iterating the array to find the current page */
        $current = 0;
        foreach ($pages['pages'] as $i => $page) {
            if($page->current) $current = $i;
        }

        /* @TODO move this into the $config initialize */
        $padding = 2;

        $total = count($pages['pages']);
        $hellip = false;
        foreach ($pages['pages'] as $i => $page) {
            $in_range = $i > ($current - $padding) && $i < ($current + $padding);

            if ($i < $padding || $in_range || $i >= ($total - $padding)) {
                $html .= '<li class="'.($page->active && !$page->current ? '' : 'active').'">';
                $html .= $this->_bootstrap_link($page, $page->page);

                $hellip = false;
            } else {
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
}