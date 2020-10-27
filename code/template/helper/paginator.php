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
            'url'        => null,
            'total'      => 0,
            'display'    => 2,
            'offset'     => 0,
            'limit'      => 0,
            'attribs'    => array(),
            'show_limit' => true,
            'show_count' => false,
        ))->append(array(
            'show_pages' => $config->count !== 1
        ));

        $html = '';

        if($config->show_limit) {
            $html .= $this->buildElement('div', ['class' => 'k-pagination__limit'], $this->limit($config));
        }

        if($config->show_pages) {
            $html .= $this->buildElement('ul', ['class' => 'k-pagination__pages'], $this->pages($config));
        }

        if($config->show_count)
        {
            $current = $this->buildElement('strong', ['class' => 'page-current'], $config->current);
            $total   = $this->buildElement('strong', ['class' => 'page-total'], $config->count);
            $text    = sprintf($this->getObject('translator')->translate('Page %s of %s'), $current, $total);

            $html .= $this->buildElement('div', ['class' => 'k-pagination-pages'], $text);
        }

        return $this->buildElement('div', ['class' => 'k-pagination'], $html);
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
            'limit'     => 0,
            'attribs' => array('class' => 'k-form-control'),
            'values'  => array(5, 10, 15, 20, 25, 30, 50, 100)
        ));

        $html     = '';
        $selected = 0;
        $options  = array();
        $values   = ObjectConfig::unbox($config->values);

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
     * Render a list of pages links
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function pages($config = array())
    {
        $config = new ModelPaginator($config);
        $config->append(array(
            'url'      => null,
            'total'    => 0,
            'display'  => 2,
            'offset'   => 0,
            'limit'    => 0,
            'show_limit' => true,
            'show_count' => false
        ))->append(array(
            'show_pages' => $config->count !== 1
        ));

        $pages = $config->pages;

        $html = '';

        $html .= $pages->previous->active ? '<li>'.$this->page($pages->previous, $config->url).'</li>' : '';

        $previous = null;
        foreach ($pages->offsets as $page)
        {
            if ($previous && $page->page - $previous->page > 1) {
                $html .= $this->buildElement('li', ['class' => 'k-is-disabled'], '<span>&hellip;</span>');
            }

            $html .= $this->buildElement('li', [
                'class' => ($page->active && !$page->current ? '' : 'k-is-active')
            ], $this->page($page, $config->url));

            $previous = $page;
        }

        if ($pages->next->active) {
            $html .= $this->buildElement('li', [], $this->page($pages->next, $config->url));
        }

        return $html;
    }

    /**
     * Render a page link
     *
     * @param   ObjectConfigInterface  $page The page data
     * @param   HttpUrlInterface       $url  The base url to create the link
     * @return  string  Html
     */
    public function page(ObjectConfigInterface $page, HttpUrlInterface $url)
    {
        $page->append(array(
            'title'   => '',
            'current' => false,
            'active'  => false,
            'offset'  => 0,
            'limit'   => 0,
            'rel'      => '',
            'attribs'  => array(),
        ));

        //Set the offset and limit
        $url->query['limit']  = $page->limit;
        $url->query['offset'] = $page->offset;

        $link_attribs = [];

        if (!empty($page->rel)) {
            $link_attribs['rel'] = $page->rel;
        }

        if ($page->active && !$page->current) {
            $link_attribs['href'] = (string)$url;
        }

        $title = is_numeric($page->title) ? $page->title : $this->getObject('translator')->translate($page->title);

        return $this->buildElement('a', $link_attribs, $title);
    }
}
