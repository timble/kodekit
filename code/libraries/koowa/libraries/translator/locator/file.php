<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * File Translator Locator
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Translator\Locator
 */
class KTranslatorLocatorFile extends KTranslatorLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'file';

    /**
     * Find a translation path
     *
     * @param array  $info  The path information
     * @return array
     */
    public function find(array $info)
    {
        $info['path'] = $info['url'];

        return parent::find($info);
    }
}
