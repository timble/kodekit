<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * String Escaper
 *
 * HTML context specific methods for use in secure output escaping
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\String\Escaper
 * @static
 */
class StringEscaper implements StringEscaperInterface
{
    /**
     * Entity Map mapping Unicode codepoints to any available named HTML entities.
     *
     * While HTML supports far more named entities, the lowest common denominator has become HTML5's XML Serialisation
     * which is restricted to the those name entities that XML supports. Using HTML entities would result in this
     * error: XML Parsing Error: undefined entity
     *
     * @var array
     */
    protected static $_entity_map = array(
        34 => 'quot',  // quotation mark
        38 => 'amp',   // ampersand
        60 => 'lt',    // less-than sign
        62 => 'gt',    // greater-than sign
    );

    /**
     * Escapde a string for a specific context
     *
     * @param string $string    The string to escape
     * @param string $context   The context. Default HTML
     * @throws \InvalidArgumentException If the context is not recognised
     * @return string
     */
    public static function escape($string, $context = 'html')
    {
        $method = 'escape'.ucfirst($context);

        if(!method_exists(__CLASS__, $method)) {
            throw new \InvalidArgumentException(sprintf('Cannot escape to: %s. Unknow context.', $context));
        }

        return static::$method($string);
    }

    /**
     * Escape Html
     *
     * Escapde a string for the HTML Body context where there are very few characters of special meaning.
     * Internally this will use htmlspecialchars().
     *
     * @param string $string
     * @return string
     */
    public static function escapeHtml($string)
    {
        return htmlspecialchars($string,  ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8', false);
    }

    /**
     * Escape Html Attribute
     *
     * This method uses an extended set of characters o escape that are not covered by htmlspecialchars() to cover
     * cases where an attribute might be unquoted or quoted illegally (e.g. backticks are valid quotes for IE).
     *
     * @param string $string
     * @return string
     */
    public static  function escapeAttr($string)
    {
        if ($string !== '' && !ctype_digit($string))
        {
            $matcher = function($matches)
            {
                $chr = $matches[0];
                $ord = ord($chr);

                /**
                 * Replace undefined characters
                 *
                 * Replace characters undefined in HTML with the hex entity for the Unicode
                 * replacement character.
                 */
                if (($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r")
                    || ($ord >= 0x7f && $ord <= 0x9f)
                ) {
                    return '&#xFFFD;';
                }

                /**
                 * Replace name entities
                 *
                 * If the current character to escape has a name entity replace it with while
                 * grabbing the integer value of the character.
                 */
                if (strlen($chr) > 1) {
                    $chr = static::convertEncoding($chr, 'UTF-16BE', 'UTF-8');
                }

                $hex = bin2hex($chr);
                $ord = hexdec($hex);
                if (isset(static::$_entity_map[$ord])) {
                    return '&' . static::$_entity_map[$ord] . ';';
                }

                /**
                 * Per OWASP recommendation
                 *
                 * Use upper hex entities for any other characters where a named entity does
                 * not exist.
                 */
                if ($ord > 255) {
                    return sprintf('&#x%04X;', $ord);
                }

                return sprintf('&#x%02X;', $ord);
            };

            $string = preg_replace_callback('/[^a-z0-9,\.\-_]/iSu', $matcher, $string);
        }

        return $string;
    }

    /**
     * Escape Javascript
     *
     * An extended set of characters are escaped beyond ECMAScript's rules for Javascript literal string escaping in
     * order to prevent misinterpretation of Javascript as HTML leading to the injection of special characters and
     * entities. The escaping used should be tolerant of cases where HTML escaping was not applied on top of
     * Javascript escaping correctly. Backslash escaping is not used as it still leaves the escaped character as-is
     * and so is not useful in a HTML context.
     *
     * @param string $string
     * @return string
     */
    public static function escapeJs($string)
    {
        if ($string !== '' && !ctype_digit($string))
        {
            $matcher = function($matches)
            {
                $chr = $matches[0];
                if (strlen($chr) == 1) {
                    return sprintf('\\x%02X', ord($chr));
                }

                $chr = static::convertEncoding($chr, 'UTF-16BE', 'UTF-8');

                return sprintf('\\u%04s', strtoupper(bin2hex($chr)));
            };

            $string = preg_replace_callback('/[^a-z0-9,\._]/iSu', $matcher, $string);
        }

        return $string;
    }

    /**
     * Escape an URL
     *
     * This should not be used to escape an entire URI - only a subcomponent being inserted. The function is a simple
     * proxy to rawurlencode() which now implements RFC 3986 since PHP 5.3 completely.
     *
     * @link https://tools.ietf.org/html/rfc3986
     *
     * @param string $string
     * @return string
     */
    public static function escapeUrl($string)
    {
        return rawurlencode($string);
    }

    /**
     * Escape CSS
     *
     * CSS escaping can be applied to any string being inserted into CSS and escapes everything except alphanumerics.
     *
     * @param string $string
     * @return string
     */
    public static function escapeCss($string)
    {
        if ($string !== '' && !ctype_digit($string))
        {
            $matcher = function($matches)
            {
                $chr = $matches[0];

                if (strlen($chr) !== 1)
                {
                    $chr = static::convertEncoding($chr, 'UTF-16BE', 'UTF-8');
                    $ord = hexdec(bin2hex($chr));
                }
                else $ord = ord($chr);

                return sprintf('\\%X ', $ord);
            };

            $string = preg_replace_callback('/[^a-z0-9]/iSu', $matcher, $string);
        }


        return $string;
    }

    /**
     * Convert encoding
     *
     * Encoding conversion helper which uses mbstring and throws and exception if it's not available.
     *
     * @param string        $string
     * @param string        $to
     * @param array|string $from
     * @throws \RuntimeException
     * @return string
     */
    public static function convertEncoding($string, $to, $from)
    {
        if (!function_exists('mb_convert_encoding'))
        {
            throw new \RuntimeException(
                'StringEscaper requires mbstring extension to be installed when escaping for non UTF-8 strings.'
            );
        }

        //Convert character encoding
        $result = mb_convert_encoding($string, $to, $from);

        // Return non-fatal blank string on encoding errors from users
        $result = $result !== false ? $result : '';

        return $result;
    }

    /**
     * Proxy static method calls
     *
     * Allow escapers to be called using a StringEscaper::[context]($string) method alias.
     *
     * @param  string     $method    The function name
     * @param  array      $arguments The function arguments
     * @throws \BadMethodCallException  If the escape method for the type doesn't exist.
     * @return mixed The result of the method
     */
    public static function __callStatic($method, $arguments)
    {
        if(empty($arguments)) {
            throw new \InvalidArgumentException(sprintf('%s() expects 1 parameter, 0 given', 'escape.'.ucfirst($method)));
        }

        return static::escape($arguments[0], $method);
    }
}
