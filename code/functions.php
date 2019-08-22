<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit {
    /**
     * Returns trailing name component of path
     *
     * Fixes a PHP issue on some locales where if the first character of the filename is non-ASCII, it is stripped.
     * See: https://stackoverflow.com/questions/32115609/basename-fail-when-file-name-start-by-an-accent
     *
     * @param  string  $path A path. On Windows, both slash (/) and backslash (\) are used as directory separator character.
     * In other environments, it is the forward slash (/).
     * @param  string  $suffix If the name component ends in suffix this will also be cut off.
     * @return string  Returns the base name of the given path.
     */
    function basename($path, $suffix = null)
    {
        return substr(\basename(' '.strtr($path, array('/' => '/ ')), $suffix), 1);
    }

    /**
     * Multi-byte-safe pathinfo replacement.
     * Drop-in replacement for pathinfo(), but multibyte-safe, cross-platform-safe, old-version-safe.
     * Works similarly to the one in PHP >= 5.2.0
     * @link http://www.php.net/manual/en/function.pathinfo.php#107461
     * @param string $path A filename or path, does not need to exist as a file
     * @param integer|string $options Either a PATHINFO_* constant,
     *      or a string name to return only the specified piece, allows 'filename' to work on PHP < 5.2
     * @return string|array
     * @copyright 2012 - 2014 Marcus Bointon
     * @copyright 2010 - 2012 Jim Jagielski
     * @copyright 2004 - 2009 Andy Prevost
     * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
     */
    function pathinfo($path, $options = null)
    {
        $ret = array('dirname' => '', 'basename' => '', 'extension' => '', 'filename' => '');
        $pathinfo = array();
        if (preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $pathinfo)) {
            if (array_key_exists(1, $pathinfo)) {
                $ret['dirname'] = $pathinfo[1];
            }
            if (array_key_exists(2, $pathinfo)) {
                $ret['basename'] = $pathinfo[2];
            }
            if (array_key_exists(5, $pathinfo)) {
                $ret['extension'] = $pathinfo[5];
            }
            if (array_key_exists(3, $pathinfo)) {
                $ret['filename'] = $pathinfo[3];
            }
        }
        switch ($options) {
            case PATHINFO_DIRNAME:
            case 'dirname':
                return $ret['dirname'];
            case PATHINFO_BASENAME:
            case 'basename':
                return $ret['basename'];
            case PATHINFO_EXTENSION:
            case 'extension':
                return $ret['extension'];
            case PATHINFO_FILENAME:
            case 'filename':
                return $ret['filename'];
            default:
                return $ret;
        }
    }
}
