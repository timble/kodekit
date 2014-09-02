<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Debug Template Helper
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Template\Helper
 */
class KTemplateHelperDebug extends KTemplateHelperBehavior
{
    /**
     * A string identifier for a known IDE/text editor,
     *
     * @var string
     */
    protected $_editor;

    /**
     * A list of known editor strings
     *
     * @var array
     */
    protected $_editors;

    /**
     * The shared paths
     *
     * An associative array containing file path mappings when running the in a vagrant box environment. The keys
     * represent the local paths while their values are the related host paths
     *
     * @var array
     */
    protected $_shared_paths;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the editors
        $this->_editor   = $config->editor;
        $this->_editors  = $config->editors;

        if (ini_get('xdebug.file_link_format') || extension_loaded('xdebug'))
        {
            // Register editor using xdebug's file_link_format option.
            $this->_editors['xdebug'] = array($this, '_getXdebugPath');

            // If no editor is set use xdebug
            if(empty($this->_editor)) {
                $this->_editor = 'xdebug';
            }
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'editor'   => '',
            'editors'  => array(
                'editor'   => 'editor://open/?file=%file&line=%line',
                'sublime'  => 'subl://open?url=file://%file&line=%line',
                'textmate' => 'txmt://open?url=file://%file&line=%line',
                'emacs'    => 'emacs://open?url=file://%file&line=%line',
                'macvim'   => 'mvim://open/?url=file://%file&line=%line',
                'phpstorm' => 'phpstorm://open?file=%file&line=%line',      //Only available in PHPStorm 8+
            ),
        ));
    }

    /**
     * Returns an HTML string of information about a single variable.
     *
     * @param   array   $config An optional array with configuration options
     *
     * @internal param mixed $value variable to dump
     * @internal param int $string_length Maximum length of strings
     * @internal param int $object_level  Object recursion limit
     * @internal param int $array_level   Array recursion limit
     * @internal param int $resources     List of supported resources, where the keys are the resources names and the
     *                                    values a Callable used to get the resource info.
     * @return  string Html
     */
    public function dump($config = array())
    {
        // Have to do this to avoid array to KObjectConfig conversion
        $value = $config['value'];
        unset($config['value']);

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'string_length'  => 128,
            'object_depth'   => 4,
            'array_depth'    => 4,
            'resources'      => array(
                'stream'         => 'stream_get_meta_data',
                'stream-context' => 'stream_context_get_options',
                'curl'           => 'curl_getinfo',
            )
        ));

        $html = '';
        if (!isset(self::$_loaded['dump']))
        {
            $html .= '<ktml:script src="media://koowa/com_koowa/js/dumper.js" />';
            $html .= '<ktml:style src="media://koowa/com_koowa/css/dumper.css" />';

            self::$_loaded['dump'] = true;
        }

        $html .= $this->_dumpVar($value, $config);

        return $html;
    }

    /**
     * Removes Joomla root from a filename replacing them with the plain text equivalents.
     *
     * Useful for debugging when you want to display a shorter path.
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function path($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'file'   => '',
            'line'   => '',
            'root'   => Koowa::getInstance()->getRootPath(),
            'editor' => $this->_editor,
        ));

        $html = $config->file;
        if (strpos($config->file, $config->root) === 0) {
            $html = '...'.DIRECTORY_SEPARATOR.trim(substr($config->file, strlen($config->root)), DIRECTORY_SEPARATOR);
        }

        if($config->editor && isset($this->_editors[$config->editor]))
        {
            $editor = $this->_editors[$config->editor];

            foreach ($this->_getSharedPaths() as $guest => $host)
            {
                if (substr($config->file, 0, strlen($guest)) == $guest) {
                    $config->file = str_replace($guest, $host, $config->file);
                }
            }

            if(is_callable($editor)) {
                $editor = call_user_func($editor, $config->file, $config->line);
            }

            $editor = str_replace("%line", $config->line, $editor);
            $editor = str_replace("%file", $config->file, $editor);

            $html = '<a href="'.$editor.'" title="'.$html.'">'.$html.'</a>';
        }

        return $html;
    }

    /**
     * Returns an HTML string, highlighting a specific line of a file, with some number of lines padded above and below.
     *
     *  @param  array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function source($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'padding' => 5,
            'file'    => '',
            'line'    => ''
        ));

        $file        = $config->file;
        $line_number = $config->line;
        $padding     = $config->padding;

        $html = '';

        // Continuing will cause errors
        if ($file && @is_readable($file))
        {
            // Open the file and set the line position
            $file = fopen($file, 'r');
            $line = 0;

            // Set the reading range
            $range = array('start' => $line_number - $padding, 'end' => $line_number + $padding);

            // Set the zero-padding amount for line numbers
            $format = '% '.strlen($range['end']).'d';

            while (($row = fgets($file)) !== FALSE)
            {
                // Increment the line number
                if (++$line > $range['end']) {
                    break;
                }

                if ($line >= $range['start'])
                {
                    // Make the row safe for output
                    $row = $this->getTemplate()->escape($row);

                    // Trim whitespace and sanitize the row
                    $row = '<span class="number">'.sprintf($format, $line).'</span> '.$row;

                    // Apply highlighting to this row
                    if ($line === $line_number) {
                        $row = '<span class="line highlight">'.$row.'</span>';
                    } else {
                        $row = '<span class="line">'.$row.'</span>';
                    }

                    // Add to the captured source
                    $html .= $row;
                }
            }

            // Close the file
            fclose($file);

            $html = '<pre class="source"><code>'.$html.'</code></pre>';
        }

        return $html;
    }

    /**
     * Returns an array of HTML strings that represent each step in the backtrace.
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function trace($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'trace'      => null,
            'statements' => array('include', 'include_once', 'require', 'require_once')
        ));

        $trace = $config->trace;

        // Start a new trace
        if ($trace === NULL) {
            $trace = debug_backtrace();
        }

        $output = array();
        foreach ($trace as $step)
        {
            // Invalid trace step
            if (!isset($step['function'])) {
                continue;
            }

            // Include the source of this step
            if (isset($step['file']) AND isset($step['line'])) {
                $source = $this->source(array('file' => $step['file'], 'line' => $step['line']));
            }

            if (isset($step['file']))
            {
                $file = $step['file'];

                if (isset($step['line'])) {
                    $line = $step['line'];
                }
            }

            // function()
            $function = $step['function'];

            // Non-standard function calls
            if (in_array($step['function'], $config->statements->toArray()))
            {
                // No arguments
                if (empty($step['args'])) {
                    $args = array();
                } else {
                    $args = array($step['args'][0]);
                }
            }
            elseif (isset($step['args']))
            {
                $params = NULL;

                // Introspection on closures or language constructs in a stack trace is impossible
                if (function_exists($step['function']) || strpos($step['function'], '{closure}') === FALSE)
                {
                    if (isset($step['class']))
                    {
                        if (method_exists($step['class'], $step['function'])) {
                            $reflection = new ReflectionMethod($step['class'], $step['function']);
                        } else {
                            $reflection = new ReflectionMethod($step['class'], '__call');
                        }
                    }
                    else  $reflection = new ReflectionFunction($step['function']);

                    // Get the function parameters
                    $params = $reflection->getParameters();
                }

                $args = array();

                foreach ($step['args'] as $i => $arg)
                {
                    if (isset($params[$i])) {
                        $args[$params[$i]->name] = $arg;  // Assign the argument by the parameter name
                    } else {
                        $args[$i] = $arg; // Assign the argument by number
                    }
                }
            }

            // Class->method() or Class::method()
            if (isset($step['class']))
            {
                $type = $step['type'];

                //Support for xdebug
                if($step['type'] == "dynamic") {
                    $type = '->';
                }

                //Support for xdebug
                if($step['type'] == "static") {
                    $type = '::';
                }

                $function = $step['class'].$type.$step['function'];
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

    /**
     * Dump a variable
     *
     * Used to handles recursion in arrays and objects.
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpVar($var, KObjectConfig $config, $level = 0)
    {
        if(!$var instanceof KObjectIdentifierInterface)
        {
            if (method_exists($this, $method = '_dump' . strtoupper(gettype($var)))) {
                $result = $this->$method($var, $config, $level);
            } else {
                $result = "<span>Unknown Type</span>\n";
            }
        }
        else $result = $this->_dumpIdentifier($var, $config);

        return $result;
    }

    /**
     * Dump a NULL
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpNull($var, KObjectConfig $config, $level = 0)
    {
        return '<span class="koowa-dump-null">NULL</span>'."\n";
    }

    /**
     * Dump a boolean
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpBoolean($var, KObjectConfig $config, $level = 0)
    {
        return '<span class="koowa-dump-bool">bool</span> '.($var ? 'TRUE' : 'FALSE');
    }

    /**
     * Dump an integer
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpInteger($var, KObjectConfig $config, $level = 0)
    {
        return '<span class="koowa-dump-integer">'.$var.'</span>';
    }

    /**
     * Dump a float
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpDouble($var, KObjectConfig $config, $level = 0)
    {
        $var = is_finite($var) ? ($tmp = json_encode($var)) . (strpos($tmp, '.') === FALSE ? '.0' : '') : var_export($var, TRUE);

        return '<span class="koowa-dump-float">'.$var.'</span>';
    }

    /**
     * Dump a string
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpString($var, KObjectConfig $config, $level = 0)
    {
        if (mb_strlen($var) > $config->string_length) {
            $str = $this->getTemplate()->escape(mb_substr($var, 0, $config->string_length)).'&nbsp;&hellip;';
        } else {
            $str = $this->getTemplate()->escape($var);
        }

        return '<span class="koowa-dump-string">string</span><span>('.strlen($var).')</span> "'.$str.'"';
    }

    /**
     * Dump an array
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpArray($var, KObjectConfig $config, $level = 0)
    {
        static $marker;

        if ($marker === NULL) {
            $marker = uniqid("\x00", TRUE);
        }

        $result = '<span class="koowa-dump-array">array</span> (';

        if (!empty($var))
        {
            if (!isset($var[$marker]))
            {
                if ($level < $config->array_depth)
                {
                    $collapsed = $level ? count($var) >= 7 : false;

                    $result .= '<span class="koowa-toggle' . ($collapsed ? ' koowa-collapsed' : '') . '">'  . count($var) . ')</span>';
                    $result .= '<div' . ($collapsed ? ' class="koowa-collapsed"' : '') . '>';

                    $var[$marker] = true;

                    foreach ($var as $key => $value)
                    {
                        if ($key !== $marker)
                        {
                            $result .= '<span class="koowa-dump-indent">   ' . str_repeat('|  ', $level) . '</span>';
                            $result .= '<span class="koowa-dump-key">' . (preg_match('#^\w+\z#', $key) ? $key : $this->getTemplate()->escape($key)) . '</span> => ';
                            $result .= $this->_dumpVar($value, $config, $level + 1);
                        }
                    }

                    unset($var[$marker]);
                    $result .= '</div>';
                }
                else $result .= count($var) . ") [ ... ]\n";
            }
            else $result .= (count($var) - 1) . ") [ <i>RECURSION</i> ]\n";
        }
        else $result .= ')'."\n";

        return $result;
    }

    /**
     * Dump an object
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpObject($var, KObjectConfig $config, $level = 0)
    {
        $fields = $this->_getObjectVars($var);

        if ($var instanceof Closure)
        {
            $rc     = new ReflectionFunction($var);
            $fields = array();

            foreach ($rc->getParameters() as $param) {
                $fields[] = '$' . $param->getName();
            }

            $fields = array(
                'file'      => $rc->getFileName(),
                'line'      => $rc->getStartLine(),
                'variables' => $rc->getStaticVariables(),
                'parameters' => implode(', ', $fields)
            );
        }

        if ($var instanceof SplFileInfo) {
            $fields = array('path' => $var->getPathname());
        }

        if ($var instanceof SplObjectStorage)
        {
            $fields = array();
            foreach (clone $var as $obj) {
                $fields[] = array('object' => $obj, 'data' => $var[$obj]);
            }
        }

        if ($var instanceof KObjectInterface) {
            unset($fields['__methods']);
        }

        if ($var instanceof KObjectManager) {
            $fields = array();
        }

        if ($var instanceof KObjectConfigInterface) {
            $fields = $var->toArray();
        }

        $result = '';
        $result .= '<span class="koowa-dump-object">' . get_class($var) . '</span>';

        if($var instanceof KObjectInterface) {
            $result .= '<span class="koowa-dump-identifier">(' . $var->getIdentifier() . ')</span>';
        }

        $result .= '<span class="koowa-dump-hash">#' . substr(md5(spl_object_hash($var)), 0, 4) . '</span>';


        static $list = array();

        if (!empty($fields))
        {
            if (!in_array($var, $list, true))
            {
                if ($level < $config->object_depth || $var instanceof Closure)
                {
                    $collapsed = $level ? count($var) >= 7 : false;

                    $result  = '<span class="koowa-toggle' . ($collapsed ? ' koowa-collapsed' : '') . '">' . $result . '</span>';
                    $result .= '<div' . ($collapsed ? ' class="koowa-collapsed"' : '') . '>';

                    $list[] = $var;

                    foreach ($fields as $key => $value)
                    {
                        $vis = '';
                        if ($key[0] === "\x00")
                        {
                            $vis = ' <span class="koowa-dump-visibility">' . ($key[1] === '*' ? 'protected' : 'private') . '</span>';
                            $key = substr($key, strrpos($key, "\x00") + 1);
                        }

                        $result .= '<span class="koowa-dump-indent">   ' . str_repeat('|  ', $level) . '</span>';
                        $result .= '<span class="koowa-dump-key">' . (preg_match('#^\w+\z#', $key) ? $key : $this->getTemplate()->escape($key)) . '</span> '.$vis.' => ';
                        $result .= $this->_dumpVar($value, $config, $level + 1);
                    }

                    array_pop($list);
                    $result .= '</div>';
                }
                else $result .= ' { ... }'."\n";
            }
            else $result .= ' { <i>RECURSION</i> }'."\n";
        }
        else $result .= "\n";

        return $result;
    }

    /**
     * Dump an object
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpResource($var, KObjectConfig $config, $level = 0)
    {
        $type = get_resource_type($var);

        $result = '<span class="koowa-dump-resource">' . $this->getTemplate()->escape($type) . ' resource</span>';

        if (isset($config->resources[$type]))
        {
            $result  = '<span class="koowa-toggle koowa-collapsed">'.$result.'</span>';
            $result .= '<div class="koowa-collapsed">';

            foreach (call_user_func($config->resources[$type], $var) as $key => $value)
            {
                $result .= '<span class="koowa-dump-indent">   ' . str_repeat('|  ', $level) . '</span>';
                $result .= '<span class="koowa-dump-key">' . $this->getTemplate()->escape($key) . "</span> => " . $this->_dumpVar($value, $config, $level + 1);
            }

            $result .= '</div>';

            return $result;
        }

        $result .= "\n";

        return $result;
    }

    /**
     * Dump an identifier
     *
     * @param   mixed         $var    Variable to dump
     * @param   KObjectConfig $config The configuration options
     * @param   integer       $level  Current recursion level (internal usage only)
     * @return  string
     */
    protected function _dumpIdentifier($var, KObjectConfig $config, $level = 0)
    {
        return '<span class="koowa-dump-identifier">'.$var.'</span>'."\n";
    }

    /**
     * Gets the properties of the given object
     *
     * Returns an associative array of defined object properties for the specified object. If a property has not been
     * assigned a value, it will be returned with a NULL value. Scope is not taken into acccount.
     *
     * @param   object   $object
     * @return  array
     */
    protected function _getObjectVars ( $object )
    {
        $vars = array();

        foreach((array) $object as $key => $value)
        {
            $aux  = explode ("\0", $key);
            $name = $aux[count($aux)-1];

            $vars[$name] = $value;
        }

        return $vars;
    }

    /**
     * Get the xddebug editor path from xdebug's file_link_format option.
     *
     * @param $file
     * @param $line
     * @return mixed
     */
    protected function _getXdebugPath($file, $line)
    {
        return str_replace(array('%f', '%l'), array($file, $line), ini_get('xdebug.file_link_format'));
    }

    /**
     * Get the shared paths if we are running in a box environment
     *
     * @return array
     */
    protected function _getSharedPaths()
    {
        $mapping = array();

        if(!$this->_shared_paths)
        {
            if ($shared_paths = getenv('BOX_SHARED_PATHS'))
            {
                $decoded = json_decode($shared_paths, true);

                if (is_array($decoded))
                {
                    $mapping = $decoded;

                    uksort($mapping, array($this, '_sharedPathComparison'));
                }
            }

            $this->_shared_paths = $mapping;
        }

        return $this->_shared_paths;
    }

    protected function _sharedPathComparison($a, $b)
    {
        if (strlen($a) == strlen($b)) {
            return 0;
        }

        if (strlen($a) > strlen($b)) {
            return -1;
        }

        return 1;
    }
}