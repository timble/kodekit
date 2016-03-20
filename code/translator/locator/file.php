<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * File Translator Locator
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Translator\Locator
 */
class TranslatorLocatorFile extends TranslatorLocatorAbstract
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
