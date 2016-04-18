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
 * Component Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object\Locator
 */
class ObjectLocatorComponent extends ObjectLocatorAbstract
{
    /**
     * The locator type
     *
     * @var string
     */
    protected static $_type = 'com';

    /**
     * Parse the identifier
     *
     * @param  ObjectIdentifier $identifier An object identifier
     * @return array
     */
    public function parseIdentifier(ObjectIdentifier $identifier)
    {
        $info = parent::parseIdentifier($identifier);

        $path  = $identifier->path;

        //Allow locating default classes if $path is empty.
        if(empty($path))
        {
            $info['path']    = $info['file'];
            $info['file']    = '';
            $info['package'] = '';
        }
        else
        {
            $package = array_shift($path);

            $info['path']    = StringInflector::implode($path);
            $info['package'] = ucfirst($package);
        }

        //Make an exception for 'view' and 'module' types
        if(in_array($info['package'], array('View','Module')) && !in_array('behavior', $path)) {
            $info['path'] = '';
        }

        return $info;
    }

    /**
     * Get the list of class templates for an identifier
     *
     * @param ObjectIdentifier $identifier The object identifier
     * @return array The class templates for the identifier
     */
    public function getClassTemplates(ObjectIdentifier $identifier)
    {
        $templates = array();

        //Identifier
        $component = $this->getObject('object.bootstrapper')
            ->getComponentIdentifier($identifier->package, $identifier->domain);

        //Fallback
        if($namespaces = $this->getIdentifierNamespaces($component))
        {
            foreach($namespaces as $namespace)
            {
                //Handle class prefix vs class namespace
                if(strpos($namespace, '\\')) {
                    $namespace .= '\\';
                }

                $templates[] = $namespace.'<Class>';
                $templates[] = $namespace.'<Package><Path><File>';
            }
        }

        $templates = array_merge($templates, parent::getClassTemplates($identifier));

        return $templates;
    }
}
