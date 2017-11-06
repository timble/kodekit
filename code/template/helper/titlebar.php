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
 * Title bar Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperTitlebar extends TemplateHelperToolbar
{
    public function getToolbarType()
    {
        return 'actionbar';
    }

    /**
     * Render the action bar commands
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'toolbar' => null,
            'title'   => null,
        ));

        //Set a custom title
        if($config->title) {
            $config->toolbar->setTitle($config->title);
        }

        $title = $this->getObject('translator')->translate($config->toolbar->getTitle());
        $html  = '';

        if (!empty($title))
        {
            $mobile = ($config->mobile === '' || $config->mobile) ? 'k-title-bar--mobile' : '';

            $html .= '
            <div class="k-title-bar k-js-title-bar '.$mobile.'">
                <div class="k-title-bar__heading">' . $title . '</div>
            </div>';
        }

        return $html;
    }
}
