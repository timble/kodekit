<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * String Inflector Interface
 *
 * Inflector to pluralize and singularize English nouns.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\String
 */
interface KStringInflectorInterface
{
   	/**
	 * Singular English word to plural.
	 *
	 * @param 	string $word Word to pluralize
	 * @return 	string Plural noun
	 */
	public static function pluralize($word);

   	/**
	 * Plural English word to singular.
	 *
	 * @param 	string $word Word to singularize.
	 * @return 	string Singular noun
	 */
	public static function singularize($word);

   	/**
	 * Returns given word as CamelCased
	 *
	 * Converts a word like "foo_bar" or "foo bar" to "FooBar". It will remove non alphanumeric characters from the
     * word, so "who's online" will be converted to "WhoSOnline"
	 *
	 * @param   string 	$word    Word to convert to camel case
	 * @return	string	UpperCamelCasedWord
	 */
	public static function camelize($word);

   	/**
	 * Converts a word "into_it_s_underscored_version"
	 *
	 * Convert any "CamelCased" or "ordinary Word" into an "underscored_word".
	 *
	 * @param  string $word  Word to underscore
	 * @return string Underscored word
	 */
	public static function underscore($word);

	/**
	 * Convert any "CamelCased" word into an array of strings
	 *
	 * Returns an array of strings each of which is a substring of string formed by splitting it at the camelcased `
     * letters.
	 *
	 * @param	string  $word Word to explode
	 * @return 	array	Array of strings
	 */
	public static function explode($word);

	/**
	 * Convert  an array of strings into a "CamelCased" word
	 *
	 * @param  array    $words   Array to implode
	 * @return string  UpperCamelCasedWord
	 */
	public static function implode($words);

   	/**
	 * Returns a human-readable string from $word
	 *
	 * Returns a human-readable string from $word, by replacing underscores with a space, and by upper-casing the
     * initial character by default.
	 *
	 * @param  string $word String to "humanize"
	 * @return string Human-readable word
     */
	public static function humanize($word);

   	/**
	 * Converts a class name to its table name according to Koowa naming conventions.
	 *
	 * Converts "Person" to "people"
	 *
	 * @param  string $className    Class name for getting related table_name.
	 * @return string plural_table_name
	 * @see classify
	 */
	public static function tableize($className);

   	/**
	 * Converts a table name to its class name according to Koowa naming conventions.
	 *
	 * Converts "people" to "Person"
	 *
	 * @see tableize()
	 * @param   string $table_name Table name for getting related ClassName.
	 * @return  string
	 */
	public static function classify($table_name);

	/**
	 * Returns camelBacked version of a string.
	 *
	 * Same as camelize but first char is lowercased.
	 *
	 * @param string $string
	 * @return string
	 * @see camelize
	 */
	public static function variablize($string);

	/**
	 * Check to see if an English word is singular
	 *
	 * @param string $string The word to check
	 * @return boolean
	 */
	public static function isSingular($string);

	/**
	 * Check to see if an English word is plural
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function isPlural($string);

    /**
     * Gets a part of a CamelCased word by index
     *
     * Use a negative index to start at the last part of the word (-1 is the last part)
     *
     * @param   string  $string  Word
     * @param   integer $index   Index of the part
     * @param   string  $default Default value
     *
     * @return  string
     */
    public static function getPart($string, $index, $default = null);
}
