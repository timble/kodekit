<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Asset Template Filter
 *
 * Filter allows to define asset url schemes that are replaced on compile and render. A default media:// scheme is
 * added that is rewritten to '/media/'.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Filter
 */
class KTemplateFilterAsset extends KTemplateFilterAbstract
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
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
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
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'schemes' => array('media://' => '/media/'),
        ));

        parent::_initialize($config);
    }

    /**
     * Add a url scheme
     *
     * @param string $alias  Scheme to be appended
     * @param mixed  $path   The path to replace the scheme
     * @return KTemplateFilterAsset
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