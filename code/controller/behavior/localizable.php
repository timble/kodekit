<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Localizable Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Behavior
 */
class KControllerBehaviorLocalizable extends KControllerBehaviorAbstract
{
    /**
     * Load the language if the controller has not been dispatched
     *
     * @param   KControllerContextInterface $context A controller context object
     * @return  void
     */
    protected function _beforeRender(KControllerContextInterface $context)
    {
        $controller = $context->getSubject();

        if (!$controller->isDispatched()) {
            $controller->loadLanguage();
        }
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
            $identifier = 'com://'.$domain.'/'.$package;
        } else {
            $identifier = 'com:'.$package;
        }

        $this->getObject('translator')->load($identifier);
    }
}