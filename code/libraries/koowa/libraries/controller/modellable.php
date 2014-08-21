<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Controller Modellable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
interface KControllerModellable
{
    /**
     * Get the controller model
     *
     * @throws  UnexpectedValueException    If the model doesn't implement the ModelInterface
     * @return	KModelInterface
     */
    public function getModel();

    /**
     * Set the controller model
     *
     * @param   mixed   $model An object that implements ObjectInterface, ObjectIdentifier object
     *                         or valid identifier string
     * @return	KControllerInterface
     */
    public function setModel($model);
}