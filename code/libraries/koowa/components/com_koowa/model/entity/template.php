<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Template entity
 *
 * @package Koowa\Component\Koowa\Model
 */
class ComKoowaModelEntityTemplate extends KModelEntityRow
{
    public function setProperties($properties, $modified = true)
    {
        parent::setProperties($properties, $modified);

        $this->path = ($this->client_id ? JPATH_ADMINISTRATOR : JPATH_ROOT) . '/templates/' . $this->template;

        return $this;
    }
}
