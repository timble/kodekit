<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Template Filterable Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template
 */
interface TemplateFilterable
{
    /**
     * Filter template content
     *
     * @return string The filtered template source
     */
    public function filter();

    /**
     * Add a filter for template transformation
     *
     * @param   mixed  $filter An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param   array $config  An optional associative array of configuration settings
     * @return TemplateInterface
     */
    public function addFilter($filter, $config = array());

    /**
     * Check if a filter exists
     *
     * @param 	string	$filter The name of the filter
     * @return  boolean	TRUE if the filter exists, FALSE otherwise
     */
    public function hasFilter($filter);

    /**
     * Create a filter by identifier
     *
     * @param   mixed $filter An object that implements ObjectInterface, ObjectIdentifier object
     *                        or valid identifier string
     * @return TemplateFilterInterface|null
     */
    public function getFilter($filter);
}