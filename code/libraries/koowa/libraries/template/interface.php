<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

 /**
  * Template Interface
  *
  * @author  Johan Janssens <https://github.com/johanjanssens>
  * @package Koowa\Library\Template
  */
interface KTemplateInterface
{
    const STATUS_LOADED    = 1;
    const STATUS_COMPILED  = 2;
    const STATUS_EVALUATED = 4;
    const STATUS_RENDERED  = 8;

    /**
     * Load a template by path
     *
     * @param   string  $path     The template path
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @param   integer $status   The template state
     * @return $this
     */
    public function load($path, $data = array(), $status = self::STATUS_LOADED);

    /**
     * Parse and compile the template to PHP code
     *
     * This function passes the template through compile filter queue and returns the result.
     *
     * @return $this
     */
    public function compile();

    /**
     * Evaluate the template using a simple sandbox
     *
     * This function writes the template to a temporary file and then includes it.
     *
     * @return $this
     * @see tempnam()
     */
    public function evaluate();

    /**
     * Render the template
     *
     * @return $this
     */
    public function render();

    /**
     * Escape a string
     *
     * By default the function uses htmlspecialchars to escape the string
     *
     * @param string $string String to to be escape
     * @return string Escaped string
     */
    public function escape($string);

    /**
     * Sets the translator object
     *
     * @param KTranslatorInterface $translator A translator object or identifier
     * @return $this
     */
    public function setTranslator(KTranslatorInterface $translator);

    /**
     * Gets the translator object
     *
     * @return  KTranslatorInterface
     */
    public function getTranslator();

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
     */
    public function translate($string, array $parameters = array());

    /**
     * Translates a string based on the number parameter passed
     *
     * @param array   $strings    Strings to choose from
     * @param integer $number     The number of items
     * @param array   $parameters An array of parameters
     *
     * @throws InvalidArgumentException
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array());

    /**
     * Get the template file identifier
     *
     * @return	string
     */
    public function getPath();

    /**
     * Get the template data
     *
     * @return	mixed
     */
    public function getData();

    /**
     * Get the template contents
     *
     * @return  string
     */
    public function getContent();

    /**
     * Set the template content from a string
     *
     * @param  string   $content     The template content
     * @param  integer  $status      The template state
     * @return $this
     */
    public function setContent($content, $status = self::STATUS_LOADED);

    /**
     * Get the format
     *
     * @return 	string 	The format of the view
     */
    public function getFormat();

    /**
     * Get the view object attached to the template
     *
     * @return  KViewInterface
     */
    public function getView();

    /**
     * Method to set a view object attached to the template
     *
     * @param mixed  $view An object that implements ObjectInterface, ObjectIdentifier object
     *                     or valid identifier string
     * @throws \UnexpectedValueException    If the identifier is not a view identifier
     * @return $this
     */
    public function setView($view);

    /**
     * Check if a filter exists
     *
     * @param 	string	$filter The name of the filter
     * @return  boolean	TRUE if the filter exists, FALSE otherwise
     */
    public function hasFilter($filter);

    /**
     * Get a filter by identifier
     *
     * @param   mixed    $filter    An object that implements ObjectInterface, ObjectIdentifier object
    or valid identifier string
     * @param   array    $config    An optional associative array of configuration settings
     * @return KTemplateFilterInterface
     */
    public function getFilter($filter, $config = array());

    /**
     * Attach a filter for template transformation
     *
     * @param   mixed  $filter An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @param   array $config  An optional associative array of configuration settings
     * @return $this
     */
    public function attachFilter($filter, $config = array());

    /**
     * Get a template helper
     *
     * @param    mixed    $helper ObjectIdentifierInterface
     * @param    array    $config An optional associative array of configuration settings
     * @return  KTemplateHelperInterface
     */
    public function getHelper($helper, $config = array());

    /**
     * Invoke a template helper method
     *
     * This function accepts a partial identifier, in the form of helper.method or schema:package.helper.method. If
     * a partial identifier is passed a full identifier will be created using the template identifier.
     *
     * If the view state have the same string keys, then the parameter value for that key will overwrite the state.
     *
     * @param    string   $identifier Name of the helper, dot separated including the helper function to call
     * @param    array    $params     An optional associative array of functions parameters to be passed to the helper
     * @return   string   Helper output
     * @throws   BadMethodCallException If the helper function cannot be called.
     */
    public function invokeHelper($identifier, $config = array());

    /**
     * Returns the template contents
     *
     * When casting to a string the template content will be compiled, evaluated and rendered.
     *
     * @return  string
     */
    public function toString();

    /**
     * Check if the template is loaded
     *
     * @return boolean  Returns TRUE if the template is loaded. FALSE otherwise
     */
    public function isLoaded();

    /**
     * Check if the template is compiled
     *
     * @return boolean  Returns TRUE if the template is compiled. FALSE otherwise
     */
    public function isCompiled();

    /**
     * Check if the template is evaluated
     *
     * @return boolean  Returns TRUE if the template is evaluated. FALSE otherwise
     */
    public function isEvaluated();

    /**
     * Check if the template is rendered
     *
     * @return boolean  Returns TRUE if the template is rendered. FALSE otherwise
     */
    public function isRendered();
}
