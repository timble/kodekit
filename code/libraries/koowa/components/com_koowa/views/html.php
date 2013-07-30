<?php
/**
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Html View
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaViewHtml extends KViewDefault
{
    /**
     * Constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Add alias filter for editor helper
        $this->getTemplate()->getFilter('alias')->append(array(
            '@editor(' => '$this->renderHelper(\'com://admin/koowa.template.helper.editor.display\', ')
        );
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        if ($this->getIdentifier()->application === 'admin' && KInflector::isSingular($this->getName()))
        {
            $config->append(array(
                'layout' => 'form'
            ));
        }


        parent::_initialize($config);
    }
}
