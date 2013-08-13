<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Debug Template Helper
 *
 * This class is based on Kohana_Debug class and subject to the BSD License
 *
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperDebug extends KTemplateHelperAbstract
{
    /**
     * Returns an HTML string of information about a single variable.
     *
     * Borrows heavily on concepts from the Debug class of [Nette](http://nettephp.com/).
     *
     * @param array $config
     * @internal param mixed $value variable to dump
     * @internal param int $length maximum length of strings
     * @internal param int $level_recursion recursion limit
     * @return  string
     */
    public function dump($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'value'           => null,
            'length'          => 128,
            'level_recursion' => 5
        ));

        return $this->_dump($config->value, $config->length, $config->level_recursion);
    }

    /**
     * Helper for Debug::dump(), handles recursion in arrays and objects.
     *
     * @param   mixed   $var    variable to dump
     * @param   integer $length maximum length of strings
     * @param   integer $limit  recursion limit
     * @param   integer $level  current recursion level (internal usage only!)
     * @return  string
     */
    protected function _dump(&$var, $length = 128, $limit = 10, $level = 0)
    {
        if ($var === NULL)
        {
            return '<small>NULL</small>';
        }
        elseif (is_bool($var))
        {
            return '<small>bool</small> '.($var ? 'TRUE' : 'FALSE');
        }
        elseif (is_float($var))
        {
            return '<small>float</small> '.$var;
        }
        elseif (is_resource($var))
        {
            if (($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var))
            {
                $meta = stream_get_meta_data($var);

                if (isset($meta['uri']))
                {
                    $file = $meta['uri'];

                    if (function_exists('stream_is_local'))
                    {
                        // Only exists on PHP >= 5.2.4
                        if (stream_is_local($file))
                        {
                            $file = $this->path($file);
                        }
                    }

                    return '<small>resource</small><span>('.$type.')</span> '.htmlspecialchars($file, ENT_NOQUOTES, 'utf-8');
                }
            }
            else
            {
                return '<small>resource</small><span>('.$type.')</span>';
            }
        }
        elseif (is_string($var))
        {
            if (mb_strlen($var) > $length)
            {
                // Encode the truncated string
                $str = htmlspecialchars(mb_substr($var, 0, $length), ENT_NOQUOTES, 'utf-8').'&nbsp;&hellip;';
            }
            else
            {
                // Encode the string
                $str = htmlspecialchars($var, ENT_NOQUOTES, 'utf-8');
            }

            return '<small>string</small><span>('.strlen($var).')</span> "'.$str.'"';
        }
        elseif (is_array($var))
        {
            $output = array();

            // Indentation for this variable
            $space = str_repeat($s = '    ', $level);

            static $marker;

            if ($marker === NULL)
            {
                // Make a unique marker
                $marker = uniqid("\x00");
            }

            if (empty($var))
            {
                // Do nothing
            }
            elseif (isset($var[$marker]))
            {
                $output[] = "(\n$space$s*RECURSION*\n$space)";
            }
            elseif ($level < $limit)
            {
                $output[] = "<span>(";

                $var[$marker] = TRUE;
                foreach ($var as $key => & $val)
                {
                    if ($key === $marker) continue;
                    if ( ! is_int($key))
                    {
                        $key = '"'.htmlspecialchars($key, ENT_NOQUOTES, 'utf-8').'"';
                    }

                    $output[] = "$space$s$key => ".$this->_dump($val, $length, $limit, $level + 1);
                }
                unset($var[$marker]);

                $output[] = "$space)</span>";
            }
            else
            {
                // Depth too great
                $output[] = "(\n$space$s...\n$space)";
            }

            return '<small>array</small><span>('.count($var).')</span> '.implode("\n", $output);
        }
        elseif (is_object($var))
        {
            // Copy the object as an array
            $array = (array) $var;

            $output = array();

            // Indentation for this variable
            $space = str_repeat($s = '    ', $level);

            $hash = spl_object_hash($var);

            // Objects that are being dumped
            static $objects = array();

            if (empty($var))
            {
                // Do nothing
            }
            elseif (isset($objects[$hash]))
            {
                $output[] = "{\n$space$s*RECURSION*\n$space}";
            }
            elseif ($level < $limit)
            {
                $output[] = "<code>{";

                $objects[$hash] = TRUE;
                foreach ($array as $key => & $val)
                {
                    if ($key[0] === "\x00")
                    {
                        // Determine if the access is protected or protected
                        $access = '<small>'.(($key[1] === '*') ? 'protected' : 'private').'</small>';

                        // Remove the access level from the variable name
                        $key = substr($key, strrpos($key, "\x00") + 1);
                    }
                    else
                    {
                        $access = '<small>public</small>';
                    }

                    $output[] = "$space$s$access $key => ".$this->_dump($val, $length, $limit, $level + 1);
                }
                unset($objects[$hash]);

                $output[] = "$space}</code>";
            }
            else
            {
                // Depth too great
                $output[] = "{\n$space$s...\n$space}";
            }

            return '<small>object</small> <span>'.get_class($var).'('.count($array).')</span> '.implode("\n", $output);
        }
        else
        {
            return '<small>'.gettype($var).'</small> '.htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, 'utf-8');
        }

        return '';
    }

    /**
     * Removes Joomla root from a filename replacing them with the plain text equivalents.
     *
     * Useful for debugging when you want to display a shorter path.
     *
     * @param array $config
     * @internal param string $file path to debug
     * @return  string
     */
    public function path($config = array())
    {
        $config = new KConfig($config);

        $file = $config->file;

        if (strpos($file, JPATH_ROOT) === 0) {
            $file = 'JPATH_ROOT'.DIRECTORY_SEPARATOR.trim(substr($file, strlen(JPATH_ROOT)), DIRECTORY_SEPARATOR);
        }

        return $file;
    }

    /**
     * Returns an HTML string, highlighting a specific line of a file, with some number of lines padded above and below.
     *
     *     // Highlights the current line of the current file
     *     echo Debug::source(__FILE__, __LINE__);
     *
     * @param array $config
     *
     * @internal param   string  $file           file to open
     * @internal param int $line line number to highlight
     * @internal param int $padding number of padding lines
     * @return  string   source of file
     * @return string file is unreadable
     */
    public function source($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'padding' => 5
        ));

        $file        = $config->file;
        $line_number = $config->line;
        $padding     = $config->padding;

        // Continuing will cause errors
        if ( ! $file OR ! is_readable($file)) {
            return FALSE;
        }

        // Open the file and set the line position
        $file = fopen($file, 'r');
        $line = 0;

        // Set the reading range
        $range = array('start' => $line_number - $padding, 'end' => $line_number + $padding);

        // Set the zero-padding amount for line numbers
        $format = '% '.strlen($range['end']).'d';

        $source = '';
        while (($row = fgets($file)) !== FALSE)
        {
            // Increment the line number
            if (++$line > $range['end'])
                break;

            if ($line >= $range['start'])
            {
                // Make the row safe for output
                $row = htmlspecialchars($row, ENT_NOQUOTES, 'utf-8');

                // Trim whitespace and sanitize the row
                $row = '<span class="number">'.sprintf($format, $line).'</span> '.$row;

                if ($line === $line_number)
                {
                    // Apply highlighting to this row
                    $row = '<span class="line highlight">'.$row.'</span>';
                }
                else
                {
                    $row = '<span class="line">'.$row.'</span>';
                }

                // Add to the captured source
                $source .= $row;
            }
        }

        // Close the file
        fclose($file);

        return '<pre class="source"><code>'.$source.'</code></pre>';
    }

    /**
     * Returns an array of HTML strings that represent each step in the backtrace.
     *
     *     // Displays the entire current backtrace
     *     echo implode('<br/>', Debug::trace());
     *
     * @param array $config
     * @internal param array $trace
     * @return  string
     */
    public function trace($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'trace' => null
        ));

        $trace = $config->trace;

        // Start a new trace
        if ($trace === NULL) {
            $trace = debug_backtrace();
        }

        // Non-standard function calls
        $statements = array('include', 'include_once', 'require', 'require_once');

        $output = array();
        foreach ($trace as $step)
        {
            if ( ! isset($step['function']))
            {
                // Invalid trace step
                continue;
            }

            if (isset($step['file']) AND isset($step['line']))
            {
                // Include the source of this step
                $source = $this->source($step['file'], $step['line']);
            }

            if (isset($step['file']))
            {
                $file = $step['file'];

                if (isset($step['line']))
                {
                    $line = $step['line'];
                }
            }

            // function()
            $function = $step['function'];

            if (in_array($step['function'], $statements))
            {
                if (empty($step['args']))
                {
                    // No arguments
                    $args = array();
                }
                else
                {
                    // Sanitize the file path
                    $args = array($step['args'][0]);
                }
            }
            elseif (isset($step['args']))
            {
                if ( ! function_exists($step['function']) OR strpos($step['function'], '{closure}') !== FALSE)
                {
                    // Introspection on closures or language constructs in a stack trace is impossible
                    $params = NULL;
                }
                else
                {
                    if (isset($step['class']))
                    {
                        if (method_exists($step['class'], $step['function']))
                        {
                            $reflection = new ReflectionMethod($step['class'], $step['function']);
                        }
                        else
                        {
                            $reflection = new ReflectionMethod($step['class'], '__call');
                        }
                    }
                    else
                    {
                        $reflection = new ReflectionFunction($step['function']);
                    }

                    // Get the function parameters
                    $params = $reflection->getParameters();
                }

                $args = array();

                foreach ($step['args'] as $i => $arg)
                {
                    if (isset($params[$i]))
                    {
                        // Assign the argument by the parameter name
                        $args[$params[$i]->name] = $arg;
                    }
                    else
                    {
                        // Assign the argument by number
                        $args[$i] = $arg;
                    }
                }
            }

            if (isset($step['class']))
            {
                // Class->method() or Class::method()
                $function = $step['class'].$step['type'].$step['function'];
            }

            $output[] = array(
                'function' => $function,
                'args'     => isset($args)   ? $args : NULL,
                'file'     => isset($file)   ? $file : NULL,
                'line'     => isset($line)   ? $line : NULL,
                'source'   => isset($source) ? $source : NULL,
            );

            unset($function, $args, $file, $line, $source);
        }

        return $output;
    }
}