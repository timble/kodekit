<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Chain Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterChain extends KObjectQueue implements KTemplateFilterCompiler, KTemplateFilterRenderer
{
    /**
     * Parse the text and compile it to PHP code
     *
     * @param string $text  The text string to parse
     * @return void
     */
    public function compile(&$text)
    {
        foreach($this as $filter)
        {
            if($filter instanceof KTemplateFilterCompiler) {
                $filter->compile($text);
            }
        }
    }

    /**
     * Parse the text and render it to html
     *
     * @param string $text  The text string to parse
     * @return void
     */
    public function render(&$text)
    {
        foreach($this as $filter)
        {
            if($filter instanceof KTemplateFilterRenderer) {
                $filter->render($text);
            }
        }
    }

    /**
     * Attach a filter to the queue
     *
     * The priority parameter can be used to override the filter priority while enqueueing the filter.
     *
     * @param   KTemplateFilterInterface  $filter
     * @param   integer          $priority The filter priority, usually between 1 (high priority) and 5 (lowest),
     *                                     default is 3. If no priority is set, the filter priority will be used
     *                                     instead.
     * @return KTemplateFilterChain
     * @throws InvalidArgumentException if the object doesn't implement KTemplateFilterInterface
     */
    public function enqueue(KObjectHandlable $filter, $priority = null)
    {
        if (!$filter instanceof KTemplateFilterInterface) {
            throw new InvalidArgumentException('Filter needs to implement TemplateFilterInterface');
        }

        $priority = is_int($priority) ? $priority : $filter->getPriority();
        return parent::enqueue($filter, $priority);
    }

    /**
     * Removes a filter from the queue
     *
     * @param   KTemplateFilterInterface   $filter
     * @return  boolean    TRUE on success FALSE on failure
     * @throws  InvalidArgumentException if the object doesn't implement KTemplateFilterInterface
     */
    public function dequeue(KObjectHandlable $filter)
    {
        if (!$filter instanceof KTemplateFilterInterface) {
            throw new InvalidArgumentException('Filter needs to implement KTemplateFilterInterface');
        }

        return parent::dequeue($filter);
    }

    /**
     * Check if the queue does contain a given filter
     *
     * @param  KTemplateFilterInterface   $filter
     * @return bool
     * @throws InvalidArgumentException if the object doesn't implement KTemplateFilterInterface
     */
    public function contains(KObjectHandlable $filter)
    {
        if (!$filter instanceof KTemplateFilterInterface) {
            throw new InvalidArgumentException('Filter needs to implement KTemplateFilterInterface');
        }

        return parent::contains($filter);
    }
}