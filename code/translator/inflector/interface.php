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
 * Translator Inflector Interface
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Kodekit\Library\Translator\Inflector
 */
interface TranslatorInflectorInterface
{
    /**
     * Returns the plural position to use for the given language and number.
     *
     * @param integer $number The number
     * @param string  $language The lnaguage
     * @return integer The plural position
     */
    public static function getPluralPosition($number, $language);

    /**
     * Overrides the default plural rule for a given language.
     *
     * @param callable $rule   A PHP callable
     * @param string $language   The language
     * @throws \LogicException
     * @return void
     */
    public static function setPluralRule(callable $rule, $language);
}