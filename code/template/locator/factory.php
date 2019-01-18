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
 * Template Locator Factory
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Locator
 */
class TemplateLocatorFactory extends FilesystemLocatorFactory
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'locators' => array(
                'lib:template.locator.component'
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Find the template path
     *
     * @param  string $url The Template url
     * @return string|false The real template path or FALSE if the template could not be found
     */
    public function locate($url)
    {
        return $this->createLocator($url)->locate($url);
    }
}