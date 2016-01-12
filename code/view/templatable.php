<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * View Templatable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
interface KViewTemplatable
{
    /**
     * Get the layout
     *
     * @return string The layout name
     */
    public function getLayout();

    /**
     * Sets the layout name to use
     *
     * @param    string  $layout The template name.
     * @return   KViewTemplatable
     */
    public function setLayout($layout);

    /**
     * Get the template object attached to the view
     *
     *  @throws	\UnexpectedValueException	If the template doesn't implement the TemplateInterface
     * @return  KTemplateInterface
     */
    public function getTemplate();

    /**
     * Method to set a template object attached to the view
     *
     * @param   mixed   $template An object that implements ObjectInterface, an object that implements
     *                            ObjectIdentifierInterface or valid identifier string
     * @throws  \UnexpectedValueException    If the identifier is not a table identifier
     * @return  KViewTemplatable
     */
    public function setTemplate($template);

}