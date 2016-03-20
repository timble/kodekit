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
 * Asset Template Filter
 *
 * Filter allows to define asset url schemes that are replaced on compile and render. A default assets:// scheme is
 * added that is rewritten to '/assets/'.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
class TemplateFilterAsset extends TemplateFilterAbstract
{
    /**
     * The schemes
     *
     * @var array
     */
    protected $_schemes;

    /**
     * Constructor.
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        foreach($config->schemes as $alias => $path) {
            $this->addScheme($alias, $path);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'schemes' => array('assets://' => '/assets/'),
            'priority' => self::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
     * Add a url scheme
     *
     * @param string $alias  Scheme to be appended
     * @param mixed  $path   The path to replace the scheme
     * @return TemplateFilterAsset
     */
    public function addScheme($alias, $path)
    {
        $this->_schemes[$alias] = $path;
        return $this;
    }

    /**
     * Convert the schemes to their real paths
     *
     * @param string $text  The text to parse
     * @return void
     */
    public function filter(&$text)
    {
        $text = str_replace(
            array_keys($this->_schemes),
            array_values($this->_schemes),
            $text);
    }
}