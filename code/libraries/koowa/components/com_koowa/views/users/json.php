<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Users JSON view
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Koowa
 */
class ComKoowaViewUsersJson extends KViewJson
{
    /**
     * Only keys returned by this method will be rendered by the view
     *
     * @return array
     */
    protected function _getAllowedKeys()
    {
        return array('id', 'name');
    }

    /**
     * User row data getter.
     *
     * Overridden for un-setting sensible data.
     *
     * @param KModelEntityInterface $entity The user row.
     *
     * @return array Associative array containing the row's data.
     */
    protected function _getUser(KModelEntityInterface $entity)
    {
        $data    = $entity->toArray();
        $allowed = $this->_getAllowedKeys();

        foreach ($data as $key => $value)
        {
            if (!in_array($key, $allowed)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Overridden to use id instead of slug for links
     *
     * {@inheritdoc}
     */
    protected function _getEntityLink(KModelEntityInterface $row)
    {
        $package = $this->getIdentifier()->package;
        $view    = 'users';

        return $this->getRoute(sprintf('option=com_%s&view=%s&id=%s&format=json', $package, $view, $row->id));
    }
}