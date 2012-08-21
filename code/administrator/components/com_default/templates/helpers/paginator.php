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
        
        $html = '';

        if ($j15) {
            $html .= '<div class="container">';
        }
        
        $html  .= '<div class="pagination">';
        if($config->show_limit) {
            $html .= '<div class="limit">'.JText::_($j15 ? 'Display NUM' : 'JGLOBAL_DISPLAY_NUM').' '.$this->limit($config).'</div>';
        }
        $html .=  $this->_pages($this->_items($config));
        if($config->show_count) {
            if ($j15) {
                $html .= '<div class="limit"> '.JText::_('Page').' '.$config->current.' '.JText::_('of').' '.$config->count.'</div>';
            } else {
                $html .= JText::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL', $config->current, $config->count);
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
        $j15 = version_compare(JVERSION, '1.6', '<');
        
        $class = $pages['first']->active ? '' : 'off';
        $html  = '<div class="button2-right '.$class.'"><div class="start">'.$this->_link($pages['first'], $j15 ? 'First' : 'JLIB_HTML_START').'</div></div>';

        $class = $pages['previous']->active ? '' : 'off';
        $html  .= '<div class="button2-right '.$class.'"><div class="prev">'.$this->_link($pages['previous'], $j15 ? 'Prev' : 'JPREV').'</div></div>';

        $html  .= '<div class="button2-left"><div class="page">';
        foreach($pages['pages'] as $page) {
            $html .= $this->_link($page, $page->page);
        }
        $html .= '</div></div>';

        $class = $pages['next']->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="next">'.$this->_link($pages['next'], $j15 ? 'Next' : 'JNEXT').'</div></div>';

        $class = $pages['last']->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="end">'.$this->_link($pages['last'], $j15 ? 'Last' : 'JLIB_HTML_END').'</div></div>';

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
            $html = '<a href="'.$url.'" '.$class.'>'.JText::_($title).'</a>';
        } else {
            $html = '<span '.$class.'>'.JText::_($title).'</span>';
        }

        return $html;
    }
}