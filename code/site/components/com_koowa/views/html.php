<?php
/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
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
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Add alias filter for editor helper
        $this->getTemplate()->getFilter('alias')->append(array(
            '@editor(' => '$this->renderHelper(\'com://admin/koowa.template.helper.editor.display\', ')
        );
    }
}