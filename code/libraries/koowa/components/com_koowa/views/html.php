<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Error Html View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class ComKoowaViewHtml extends KViewDefault
{
    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Add alias filter for editor helper
        $this->getTemplate()->getFilter('alias')->append(array(
            '@editor('  => '$this->renderHelper(\'editor.display\', '
        ));
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        //Add the module template filter
        $config->append(array(
            'template_filters'	=> array('module'),
        ));

        //Set the default layout to form for singular admin views
        if ($this->getIdentifier()->application === 'admin' && KStringInflector::isSingular($this->getName()))
        {
            $config->append(array(
                'layout' => 'form'
            ));
        }

        parent::_initialize($config);
    }
}
