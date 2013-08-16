<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Template Filter Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
interface KTemplateFilterInterface  extends KCommandInterface
{
  	/**
     * Get the template object
     *
     * @return  object	The template object
     */
    public function getTemplate();

    /**
     * Sets the template object
     *
     * @param string|KTemplateInterface $template A template object or identifier
     * @return $this
     */
    public function setTemplate($template);
}
