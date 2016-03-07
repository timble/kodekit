<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Localizable View Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View\Behavior
 */
class KViewBehaviorLocalizable extends KViewBehaviorAbstract
{
    /**
     * Load the language
     *
     * @param   KViewContextInterface $context A view context object
     * @return  void
     */
    protected function _beforeRender(KViewContextInterface $context)
    {
        $context->getSubject()->loadLanguage();
    }

    /**
     * Load the translations
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