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
  * Template Interface
  *
  * @author  Johan Janssens <https://github.com/johanjanssens>
  * @package Kodekit\Library\Template
  */
interface TemplateInterface
{
    /**
     * Render a template
     *
     * @param   string  $source  The template url or content
     * @param   array   $data    An associative array of data to be extracted in local template scope
     * @return  string  The rendered template source
     */
    public function render($source, array $data = array());

    /**
     * Get a template data property
     *
     * @param   string  $property The property name.
     * @param   mixed   $default  Default value to return.
     * @return  string  The property value.
     */
    public function get($property, $default = null);

    /**
     * Get the template data
     *
     * @return  array   The template data
     */
    public function getData();

    /**
     * Register a template function
     *
     * @param string  $name      The function name
     * @param string  $function  The callable
     * @return TemplateInterface
     */
    public function registerFunction($name, $function);

    /**
     * Unregister a template function
     *
     * @param string    $name   The function name
     * @return TemplateInterface
     */
    public function unregisterFunction($name);
}
