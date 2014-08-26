<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * File Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Locator
 */
class KTemplateLocatorFile extends KTemplateLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'file';

    /**
     * Find a template path
     *
     * @param array  $info  The path information
     * @return bool|mixed
     */
    public function find(array $info)
    {
        //Qualify partial templates.
        if(is_file($info['url']) === false)
        {
            if(empty($info['base'])) {
                throw new RuntimeException('Cannot qualify partial template path');
            }

            $path = dirname($info['base']);
        }
        else $path = dirname($info['url']);

        $file   = pathinfo($info['url'], PATHINFO_FILENAME);
        $format = pathinfo($info['url'], PATHINFO_EXTENSION);

        if($result = $this->realPath($path.'/'.$file.'.'.$format)) {
            return $result;
        }

        return false;
    }
}