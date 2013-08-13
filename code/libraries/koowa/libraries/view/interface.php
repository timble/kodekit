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
	 * Get the model object attached to the contoller
	 *
	 * @return	KModelInterface
	 */
	public function getModel();

	/**
	 * Method to set a model object attached to the view
	 *
	 * @param	mixed	$model An object that implements KObjectInterface, KServiceIdentifier object
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
     * Get the layout.
     *
     * @return string The layout name
     */
    public function getLayout();

   /**
     * Sets the layout name to use
     *
     * @param    string  $layout The template name.
     * @return   KViewAbstract
     */
    public function setLayout($layout);

	/**
	 * Create a route based on a full or partial query string
	 *
	 * index.php, option, view and layout can be ommitted. The following variations
	 * will all result in the same route
	 *
	 * - foo=bar
	 * - option=com_mycomp&view=myview&foo=bar
	 * - index.php?option=com_mycomp&view=myview&foo=bar
	 *
	 * If the route starts '&' the information will be appended to the current URL.
	 *
	 * In templates, use @route()
	 *
	 * @param	string	$route The query string used to create the route
	 * @return 	string 	The route
	 */
	public function createRoute( $route = '');
}
