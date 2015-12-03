<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator Inflector Interface
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Translator\Inflector
 */
interface KTranslatorInflectorInterface
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