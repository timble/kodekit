<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Koowa Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Locator
 */
class KObjectLocatorLibrary extends KObjectLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'lib';

    /**
     * Get the list of location templates for an identifier
     *
     * @param string $identifier The package identifier
     * @return string The class location templates for the identifier
     */
    public function getClassTemplates($identifier)
    {
        $templates = array(
            'K<Package><Class>',
            'K<Package><Path>Default',
        );

        return $templates;
    }
}
