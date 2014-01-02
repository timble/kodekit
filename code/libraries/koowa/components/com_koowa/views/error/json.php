<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Exception Json View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaViewErrorJson extends KViewJson
{
    protected function _actionRender(KViewContext $context)
    {
        $properties = array(
            'message' => $this->message,
            'code'    => $this->code
        );

        if(ini_get('display_errors'))
        {
            $properties['data'] = array(
                'file'	   => $this->file,
                'line'     => $this->line,
                'function' => $this->function,
                'class'	   => $this->class,
                'args'	   => $this->args,
                'info'	   => $this->info
            );
        }

        $content = json_encode(array(
            'version'  => '1.0',
            'errors'   => array($properties)
        ));

        $this->setContent($content);

        return parent::_actionRender($context);
    }
}