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
class ComKoowaTemplateHelperPaginator extends KTemplateHelperPaginator
{
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
            $html .= sprintf($this->translate('JLIB_HTML_PAGE_CURRENT_OF_TOTAL'), $config->current, $config->count);
        }

        $html .= '</div>';

        return $html;
    }
}
