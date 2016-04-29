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
 * Helperable Template Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Behavior
 */
class TemplateBehaviorHelperable extends TemplateBehaviorAbstract
{
    /**
     * Register a helper() function in the template
     *
     * @param ViewContextInterface $context A view context object
     * @return void
     */
    protected function _beforeRender(TemplateContextInterface $context)
    {
        $context->subject->registerFunction('helper', array($this, 'invokeHelper'));
    }

    /**
     * Invoke a template helper
     *
     * This function accepts a partial identifier, in the form of helper.method or schema:package.helper.method. If
     * a partial identifier is passed a full identifier will be created using the template identifier.
     *
     * If the state have the same string keys, then the parameter value for that key will overwrite the state.
     *
     * @param    string   $identifier Name of the helper, dot separated including the helper function to call
     * @param    array    $params     An optional associative array of functions parameters to be passed to the helper
     * @return   string   Helper output
     * @throws   \BadMethodCallException If the helper function cannot be called.
     */
    public function invokeHelper($identifier, $params = array())
    {
        //Get the function and helper based on the identifier
        $parts      = explode('.', $identifier);
        $function   = array_pop($parts);
        $identifier = array_pop($parts);

        //Handle schema:package.helper.function identifiers
        if(!empty($parts)) {
            $identifier = implode('.', $parts).'.template.helper.'.$identifier;
        }

        //Create the complete identifier if a partial identifier was passed
        if (is_string($identifier) && strpos($identifier, '.') === false)
        {
            $helper = $this->getMixer()->getIdentifier()->toArray();

            if($helper['type'] != 'lib') {
                $helper['path'] = array('template', 'helper');
            } else {
                $helper['path'] = array('helper');
            }

            $helper['name'] = $identifier;
        }
        else $helper = $this->getIdentifier($identifier);

        $helper = $this->getObject('template.helper.factory')->createHelper($helper, ObjectConfig::unbox($params));

        //Call the helper function
        if (!is_callable(array($helper, $function))) {
            throw new \BadMethodCallException(get_class($helper) . '::' . $function . ' not supported.');
        }

        //Merge the parameters if helper asks for it
        if ($helper instanceof TemplateHelperParameterizable) {
            $params = array_merge($this->getParameters()->toArray(), $params);
        }

        return $helper->$function($params, $this->getMixer());
    }
}