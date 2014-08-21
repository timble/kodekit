<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Users JSON view
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Koowa
 */
class ComKoowaViewUsersJson extends KViewJson
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Only allow fields in the config option for security reasons
        $this->_fields = array_intersect(KObjectConfig::unbox($config->fields), $this->_fields);
    }

    protected function _initialize(KObjectConfig $config)
    {
        if (empty($config->fields)) {
            $config->fields = array('id', 'name');
        }

        parent::_initialize($config);
    }

    /**
     * Overridden to use id instead of slug for links
     *
     * {@inheritdoc}
     */
    protected function _getEntityRoute(KModelEntityInterface $entity)
    {
        $package = $this->getIdentifier()->package;
        $view    = 'users';

        return $this->getRoute(sprintf('option=com_%s&view=%s&id=%s&format=json', $package, $view, $entity->id));
    }
}