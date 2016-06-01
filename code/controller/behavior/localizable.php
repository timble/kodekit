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
 * Localizable Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller\Behavior
 */
class ControllerBehaviorLocalizable extends ControllerBehaviorAbstract
{
    /**
     * Load the language if the controller has not been dispatched
     *
     * @param   ControllerContext $context A controller context object
     * @return  void
     */
    protected function _beforeRender(ControllerContext $context)
    {
        $controller = $context->getSubject();

        if (!$controller->isDispatched()) {
            $controller->loadLanguage();
        }
    }

    /**
     * Get the language
     *
     * Returns a properly formatted language tag, eg xx-XX
     * @link https://en.wikipedia.org/wiki/IETF_language_tag
     * @link https://tools.ietf.org/html/rfc5646
     *
     * @return string|null The language tag
     */
    public function getLanguage()
    {
        return $this->getObject('translator')->getLanguage();
    }

    /**
     * Load the language
     *
     * @return 	void
     */
    public function loadLanguage()
    {
        $package = $this->getIdentifier()->package;
        $domain  = $this->getIdentifier()->domain;

        if($domain) {
            $url = 'com://'.$domain.'/'.$package;
        } else {
            $url = 'com:'.$package;
        }

        $this->getObject('translator')->load($url);
    }
}