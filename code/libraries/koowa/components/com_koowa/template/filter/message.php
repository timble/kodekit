<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Messages Template Filter
 *
 * Filter will render the response flash messages.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComKoowaTemplateFilterMessage extends KTemplateFilterAbstract
{
    public function filter(&$text)
    {
        if (strpos($text, '<ktml:messages>') !== false)
        {
            $output   = '';
            $messages = $this->getObject('response')->getMessages();

            foreach ($messages as $type => $message)
            {
                if ($type === 'notice') {
                    $type = 'info';
                }

                $output .= '<div class="alert alert-'.strtolower($type).'">';
                foreach ($message as $line) {
                    $output .= '<div class="alert__text">'.$line.'</div>';
                }
                $output .= '</div>';
            }

            $text = str_replace('<ktml:messages>', $output, $text);
        }
    }
}