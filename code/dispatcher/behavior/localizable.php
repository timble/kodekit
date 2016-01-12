<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Localizable Dispatcher Behavior
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher\Behavior
 */
class KDispatcherBehaviorLocalizable extends KDispatcherBehaviorAbstract
{
    /**
     * Load the language
     *
     * @param   KViewContextInterface $context A view context object
     * @return  void
     */
    protected function _beforeDispatch(KDispatcherContextInterface $context)
    {
        $context->getSubject()->loadLanguage();
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