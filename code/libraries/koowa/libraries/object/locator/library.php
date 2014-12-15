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
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'sequence' => array(
                'K<Package><Class>',
                'K<Package><Path>Default',
            )
        ));

        parent::_initialize($config);
    }
}
