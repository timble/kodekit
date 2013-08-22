<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * View Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
interface KViewInterface
{
    /**
     * Return the views output
     *
     * @return string 	The output of the view
     */
    public function display();

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
     */
    public function translate($string, array $parameters = array());

    /**
	 * Get the name
	 *
	 * @return 	string 	The name of the object
	 */
	public function getName();

	/**
	 * Get the format
	 *
	 * @return 	string 	The format of the view
	 */
	public function getFormat();

    /**
     * Get the content
     *
     * @return  string The content of the view
     */
    public function getContent();

    /**
     * Get the content
     *
     * @param  string $content The content of the view
     * @return KViewInterface
     */
    public function setContent($content);

	/**
	 * Get the model object attached to the controller
	 *
	 * @return	KModelInterface
	 */
	public function getModel();

	/**
	 * Method to set a model object attached to the view
	 *
	 * @param	mixed	$model An object that implements KObjectInterface, KObjectIdentifier object
	 * 					       or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a table identifier
	 * @return	KViewInterface
	 */
    public function setModel($model);

    /**
     * Gets the translator object
     *
     * @return  KTranslator
     */
    public function getTranslator();

    /**
     * Sets the translator object
     *
     * @param string|KTranslator $translator A translator object or identifier
     * @return KViewInterface
     */
    public function setTranslator($translator);

    /**
     * Create a route based on a full or partial query string
     *
     * index.php? will be automatically added.
     * option, view and layout can be omitted. The following variations will result in the same route:
     *
     * - foo=bar
     * - view=myview&foo=bar
     * - option=com_mycomp&view=myview&foo=bar
     *
     * If the route starts with '&' the query string will be appended to the current URL.
     *
     * In templates, use @route()
     *
     * @param   string|array $route  The query string or array used to create the route
     * @param   boolean      $fqr    If TRUE create a fully qualified route. Defaults to FALSE.
     * @param   boolean      $escape If TRUE escapes the route for xml compliance. Defaults to TRUE.
     * @return  string The route
     */
    public function createRoute($route, $fqr = null, $escape = null);
}
