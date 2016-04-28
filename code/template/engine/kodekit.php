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
 * Kodekit Template Engine
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Engine
 */
class TemplateEngineKodekit extends TemplateEngineAbstract
{
    /**
     * The engine file types
     *
     * @var string
     */
    protected static $_file_types = array('php');

    /**
     * The template buffer
     *
     * @var FilesystemStreamBuffer
     */
    protected static $__buffer;

    /**
     * Constructor
     *
     * @param ObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Create the buffer if we are not using a file cache
        if(!$this->isCache())
        {
            static::$__buffer = $this->getObject('filesystem.stream.factory')
                ->createStream('kodekit-buffer://temp', 'w+b');
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'functions'           => array(
                'import' => array($this, 'renderPartial'),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Render a template
     *
     * @param   string  $source   A fully qualified template url or content string
     * @param   array   $data     An associative array of data to be extracted in local template scope
     * @throws \InvalidArgumentException If the template could not be located
     * @throws \RuntimeException         If a partial template url could not be fully qualified
     * @throws \RuntimeException         If the template could not be loaded
     * @throws \RuntimeException         If the template could not be compiled

     * @return string The rendered template source
     */
    public function render($source, array $data = array())
    {
        $source = parent::render($source, $data);

        //Load the template
        if($this->getObject('filter.path')->validate($source))
        {
            $source_file = $this->locateSource($source);

            if(!$cache_file = $this->isCached($source_file))
            {
                $source     = $this->loadSource($source_file);
                $source     = $this->compileSource($source);
                $cache_file = $this->cacheSource($source_file, $source);
            }
        }
        else
        {
            $name        = crc32($source);
            $source_file = '';

            if(!$cache_file = $this->isCached($name))
            {
                $source     = $this->compileSource($source);
                $cache_file = $this->cacheSource($name, $source);
            }
        }

        //Evaluate the template
        $result = $this->evaluateSource($cache_file);

        //Render the debug information
        $result = $this->renderDebug($result);

        return $result;
    }

    /**
     * Cache the compiled template source
     *
     * Write the template content to a file buffer. If cache is enabled the file will be buffer using cache settings
     * If caching is not enabled the file will be written to the temp path using a buffer://temp stream.
     *
     * @param  string $name     The file name
     * @param  string $source   The template source to cache
     * @throws \RuntimeException If the template cache path is not writable
     * @throws \RuntimeException If template cannot be cached
     * @return string The cached template file path
     */
    public function cacheSource($name, $source)
    {
        if(!$file = parent::cacheSource($name, $source))
        {
            static::$__buffer->truncate(0);
            static::$__buffer->write($source);

            $file = static::$__buffer->getPath();
        }

        return $file;
    }

    /**
     * Compile the template source
     *
     * If the a compile error occurs and exception will be thrown if the error cannot be recovered from or if debug
     * is enabled.
     *
     * @param  string $source The template source to compile
     * @throws TemplateExceptionSyntaxError
     * @return string The compiled template content
     */
    public function compileSource($source)
    {
        //Convert PHP tags
        if (!ini_get('short_open_tag'))
        {
            // convert "<?=" to "<?php echo"
            $find = '/\<\?\s*=\s*(.*?)/';
            $replace = "<?php echo \$1";
            $source = preg_replace($find, $replace, $source);

            // convert "<?" to "<?php"
            $find = '/\<\?(?:php)?\s*(.*?)/';
            $replace = "<?php \$1";
            $source = preg_replace($find, $replace, $source);
        }

        //Get the template functions
        $functions = $this->getFunctions();

        //Compile to valid PHP
        $tokens   = token_get_all($source);

        $result = '';
        for ($i = 0; $i < sizeof($tokens); $i++)
        {
            if(is_array($tokens[$i]))
            {
                list($token, $content) = $tokens[$i];

                switch ($token)
                {
                    //Proxy registered functions through __call()
                    case T_STRING :

                        if(isset($functions[$content]) )
                        {
                            $prev = (array) $tokens[$i-1];
                            $next = (array) $tokens[$i+1];

                            if($next[0] == '(' && $prev[0] !== T_OBJECT_OPERATOR) {
                                $result .= '$this->'.$content;
                                break;
                            }
                        }

                        $result .= $content;
                        break;

                    //Do not allow to use $this context
                    case T_VARIABLE:

                        if ('$this' == $content) {
                            throw new TemplateExceptionSyntaxError('Using $this when not in object context');
                        }

                        $result .= $content;
                        break;

                    default:
                        $result .= $content;
                        break;
                }
            }
            else $result .= $tokens[$i] ;
        }

        return $result;
    }

    /**
     * Evaluate the template using a simple sandbox
     *
     * @param  string $path The template path
     * @throws \RuntimeException If the template could not be evaluated
     * @return string The evaluated template content
     */
    public function evaluateSource($path)
    {
        ob_start();

        extract($this->getData(), EXTR_SKIP);
        include $path;
        if(!$result = ob_get_clean()) {
            throw new \RuntimeException(sprintf('The template "%s" cannot be evaluated.', $path));
        }

        return $result;
    }
}