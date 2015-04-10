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
     * @throws RuntimeException If the no base path exists while trying to locate a partial.
     * @return string|false The real template path or FALSE if the template could not be found
     */
    public function find(array $info)
    {
        //Qualify partial templates.
        if(dirname($info['url']) === '.')
        {
            if(empty($info['base'])) {
                throw new RuntimeException('Cannot qualify partial template path');
            }

            $path = dirname($info['base']);
        }
        else $path = dirname($info['url']);

        $file   = pathinfo($info['url'], PATHINFO_FILENAME);
        $format = pathinfo($info['url'], PATHINFO_EXTENSION);
        $path   = str_replace(parse_url($path, PHP_URL_SCHEME).'://', '', $path);

        if(!$result = $this->realPath($path.'/'.$file.'.'.$format))
        {
            $pattern = $path.'/'.$file.'.'.$format.'.*';
            $results = glob($pattern);

            //Try to find the file
            if ($results)
            {
                foreach($results as $file)
                {
                    if($result = $this->realPath($file)) {
                        break;
                    }
                }
            }

        }

        return $result;
    }
}
