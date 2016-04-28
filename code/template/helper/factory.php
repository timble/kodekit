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
 * Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperFactory extends Object implements ObjectSingleton
{
    /**
     * Get a template helper
     *
     * @param    mixed $helper ObjectIdentifierInterface
     * @param    array $config An optional associative array of configuration settings
     * @throws  \UnexpectedValueException
     * @return  TemplateHelperInterface
     */
    public function createHelper($helper, array $config = array())
    {
        if(is_string($helper) && strpos($helper, '.') === false )
        {
            $identifier = $this->getIdentifier()->toArray();
            $identifier['name'] = $helper;
        }
        else $identifier = $helper;

        //Create the template helper
        $helper = $this->getObject($identifier, $config);

        //Check the helper interface
        if (!($helper instanceof TemplateHelperInterface))
        {
            throw new \UnexpectedValueException(
                "Template helper $identifier does not implement TemplateHelperInterface"
            );
        }

        return $helper;
    }
}
