<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * APC 3.1.4 compatibility
 */
if(extension_loaded('apc') && !function_exists('apc_exists'))
{
    /**
     * Check if an APC key exists
     *
     * @param  mixed  $keys A string, or an array of strings, that contain keys.
     * @return boolean Returns TRUE if the key exists, otherwise FALSE
     */
    function apc_exists($keys)
    {
        $result = null;

        apc_fetch($keys,$result);

        return $result;
    }
}

/**
 * PHP5.4 compatibility
 *
 * @link http://nikic.github.io/2012/01/28/htmlspecialchars-improvements-in-PHP-5-4.html
 */
if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', ENT_IGNORE); //PHP 5.3 behavior
}

/**
 * mbstring compatibility
 *
 * @link http://php.net/manual/en/book.mbstring.php
 */

if (!function_exists('mb_strlen'))
{
    function mb_strlen($str)
    {
        return strlen(utf8_decode($str));
    }
}

if (!function_exists('mb_substr'))
{
    /*
     * Joomla checks if mb_substr exists to determine the availability of mbstring extension
     * Loading JString before providing the replacement function makes sure everything works
     */
    if (class_exists('JLoader') && is_callable(array('JLoader', 'import')))
    {
        JLoader::import('joomla.string.string');
        JLoader::load('JString');
    }

    function mb_substr($str, $offset, $length = NULL)
    {
        // generates E_NOTICE
        // for PHP4 objects, but not PHP5 objects
        $str = (string)$str;
        $offset = (int)$offset;
        if (!is_null($length)) $length = (int)$length;

        // handle trivial cases
        if ($length === 0) return '';
        if ($offset < 0 && $length < 0 && $length < $offset)
            return '';

        // normalise negative offsets (we could use a tail
        // anchored pattern, but they are horribly slow!)
        if ($offset < 0) {

            // see notes
            $strlen = strlen(utf8_decode($str));
            $offset = $strlen + $offset;
            if ($offset < 0) $offset = 0;

        }

        $Op = '';
        $Lp = '';

        // establish a pattern for offset, a
        // non-captured group equal in length to offset
        if ($offset > 0) {

            $Ox = (int)($offset/65535);
            $Oy = $offset%65535;

            if ($Ox) {
                $Op = '(?:.{65535}){'.$Ox.'}';
            }

            $Op = '^(?:'.$Op.'.{'.$Oy.'})';

        } else {

            // offset == 0; just anchor the pattern
            $Op = '^';

        }

        // establish a pattern for length
        if (is_null($length)) {

            // the rest of the string
            $Lp = '(.*)$';

        } else {

            if (!isset($strlen)) {
                // see notes
                $strlen = strlen(utf8_decode($str));
            }

            // another trivial case
            if ($offset > $strlen) return '';

            if ($length > 0) {

                // reduce any length that would
                // go passed the end of the string
                $length = min($strlen-$offset, $length);

                $Lx = (int)( $length / 65535 );
                $Ly = $length % 65535;

                // negative length requires a captured group
                // of length characters
                if ($Lx) $Lp = '(?:.{65535}){'.$Lx.'}';
                $Lp = '('.$Lp.'.{'.$Ly.'})';

            } else if ($length < 0) {

                if ( $length < ($offset - $strlen) ) {
                    return '';
                }

                $Lx = (int)((-$length)/65535);
                $Ly = (-$length)%65535;

                // negative length requires ... capture everything
                // except a group of  -length characters
                // anchored at the tail-end of the string
                if ($Lx) $Lp = '(?:.{65535}){'.$Lx.'}';
                $Lp = '(.*)(?:'.$Lp.'.{'.$Ly.'})$';

            }

        }

        if (!preg_match( '#'.$Op.$Lp.'#us',$str, $match )) {
            return '';
        }

        return $match[1];

    }
}
