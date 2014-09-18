<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Exception Json View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\View\Error
 */
class ComKoowaViewErrorJson extends KViewJson
{
    protected function _actionRender(KViewContext $context)
    {
        if(ini_get('display_errors')) {
            $this->message = $this->exception ." with message '".$this->message."' in ".$this->file.":".$this->line;
        }

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