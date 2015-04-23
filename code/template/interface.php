<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

 /**
  * Template Interface
  *
  * @author  Johan Janssens <https://github.com/johanjanssens>
  * @package Koowa\Library\Template
  */
interface KTemplateInterface
{
    /**
     * Load a template by url
     *
     * @param   string  $url    The template url
     * @throws \InvalidArgumentException If the template could not be located
     * @return KTemplateInterface
     */
    public function loadFile($url);

    /**
     * Load a template from a string
     *
     * @param  string   $content The template content
     * @return KTemplateInterface
     */
    public function loadString($content);

    /**
     * Render the template
     *
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @return string The rendered template source
     */
    public function render(array $data = array());

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
     * @return KTemplateInterface
     */
    public function registerFunction($name, $function);

    /**
     * Unregister a template function
     *
     * @param string    $name   The function name
     * @return KTemplateInterface
     */
    public function unregisterFunction($name);
}
